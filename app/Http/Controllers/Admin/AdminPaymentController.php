<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    /**
     * Admin payment dashboard
     */
    public function index()
    {
        $waitingPayments = Payment::with(['user', 'surveyBooking'])
            ->where('status', 'waiting_confirmation')
            ->orderBy('paid_at', 'desc')
            ->paginate(15);

        $stats = [
            'waiting' => Payment::where('status', 'waiting_confirmation')->count(),
            'confirmed_today' => Payment::where('status', 'confirmed')
                ->whereDate('confirmed_at', today())
                ->count(),
            'total_dp_today' => Payment::where('status', 'confirmed')
                ->where('payment_type', 'dp')
                ->whereDate('confirmed_at', today())
                ->sum('paid_amount'),
            'total_revenue_today' => Payment::where('status', 'confirmed')
                ->whereDate('confirmed_at', today())
                ->sum('paid_amount'),
        ];

        return view('admin.payments.index', compact('waitingPayments', 'stats'));
    }

    /**
     * Show payment detail
     */
    public function show(Payment $payment)
    {
        $payment->load(['user', 'surveyBooking', 'order']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Confirm payment
     */
    public function confirm(Request $request, Payment $payment)
    {
        if ($payment->status !== 'waiting_confirmation') {
            return redirect()->back()->with('error', 'Payment sudah dikonfirmasi atau ditolak.');
        }

        $payment->update([
            'status' => 'confirmed',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        // If DP payment confirmed, create order
        if ($payment->payment_type === 'dp') {
            $order = Order::create([
                'survey_booking_id' => $payment->survey_booking_id,
                'user_id' => $payment->user_id,
                'total_price' => $payment->total_price,
                'dp_paid' => $payment->paid_amount,
                'remaining_amount' => $payment->remaining_amount,
                'status' => 'dp_confirmed',
                'progress_percentage' => 0,
                'current_stage' => 'Menunggu produksi dimulai',
            ]);

            // Add initial progress
            $order->addProgressUpdate('Order dibuat', 'DP telah dikonfirmasi, menunggu produksi dimulai');

            // Create notification for order creation
            Notification::create([
                'user_id' => $payment->user_id,
                'type' => 'order_progress',
                'survey_booking_id' => $payment->survey_booking_id,
                'title' => 'Order Dibuat',
                'message' => "Order {$order->order_number} berhasil dibuat. DP telah dikonfirmasi, pekerjaan akan segera dimulai.",
                'data' => json_encode(['order_id' => $order->id]),
            ]);
        }

        // If remaining payment confirmed, update order
        if ($payment->payment_type === 'remaining') {
            $order = Order::where('survey_booking_id', $payment->survey_booking_id)->first();
            if ($order) {
                $order->update([
                    'remaining_paid' => $payment->paid_amount,
                    'status' => 'completed',
                    'completed_at' => now(),
                    'progress_percentage' => 100,
                    'current_stage' => 'Selesai - Siap diambil',
                ]);

                $order->addProgressUpdate('Pelunasan dikonfirmasi', 'Pembayaran telah lunas, barang siap diambil');
            }
        }

        // Create notification for user
        Notification::create([
            'user_id' => $payment->user_id,
            'type' => 'payment',
            'survey_booking_id' => $payment->survey_booking_id,
            'title' => 'Pembayaran Dikonfirmasi',
            'message' => "Pembayaran {$payment->payment_type} sebesar Rp " . number_format($payment->paid_amount, 0, ',', '.') . " telah dikonfirmasi.",
        ]);

        return redirect()->back()->with('success', 'Payment berhasil dikonfirmasi.');
    }

    /**
     * Reject payment
     */
    public function reject(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        if ($payment->status !== 'waiting_confirmation') {
            return redirect()->back()->with('error', 'Payment sudah dikonfirmasi atau ditolak.');
        }

        $payment->update([
            'status' => 'rejected',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
            'payment_notes' => ($payment->payment_notes ? $payment->payment_notes . "\n\n" : '') . "DITOLAK: " . $validated['rejection_reason'],
        ]);

        // Create notification for user
        Notification::create([
            'user_id' => $payment->user_id,
            'type' => 'payment',
            'survey_booking_id' => $payment->survey_booking_id,
            'title' => 'Pembayaran Ditolak',
            'message' => "Pembayaran {$payment->payment_type} sebesar Rp " . number_format($payment->paid_amount, 0, ',', '.') . " ditolak. Alasan: {$validated['rejection_reason']}",
        ]);

        return redirect()->back()->with('success', 'Payment ditolak.');
    }

    /**
     * All payments with filters
     */
    public function allPayments(Request $request)
    {
        $query = Payment::with(['user', 'surveyBooking']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_type') && $request->payment_type !== '') {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        $totalStats = [
            'total_dp' => Payment::where('status', 'confirmed')
                ->where('payment_type', 'dp')
                ->sum('paid_amount'),
            'total_remaining' => Payment::where('status', 'confirmed')
                ->where('payment_type', 'remaining')
                ->sum('paid_amount'),
            'total_revenue' => Payment::where('status', 'confirmed')
                ->sum('paid_amount'),
        ];

        return view('admin.payments.all', compact('payments', 'totalStats'));
    }
}
