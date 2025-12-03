<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrderController extends Controller
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
     * Admin order dashboard
     */
    public function index()
    {
        $activeOrders = Order::with(['user', 'surveyBooking'])
            ->whereIn('status', ['dp_confirmed', 'in_progress', 'ready_for_pickup'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'dp_confirmed' => Order::where('status', 'dp_confirmed')->count(),
            'in_progress' => Order::where('status', 'in_progress')->count(),
            'ready_for_pickup' => Order::where('status', 'ready_for_pickup')->count(),
            'completed_today' => Order::where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
            'total_revenue_month' => Order::where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->sum('total_price'),
        ];

        return view('admin.orders.index', compact('activeOrders', 'stats'));
    }

    /**
     * Show order detail
     */
    public function show(Order $order)
    {
        $order->load(['user', 'surveyBooking', 'payments']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order progress
     */
    public function updateProgress(Request $request, Order $order)
    {
        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'current_stage' => 'required|string',
            'progress_note' => 'nullable|string',
        ]);

        $oldProgress = $order->progress_percentage;
        
        $order->update([
            'progress_percentage' => $validated['progress_percentage'],
            'current_stage' => $validated['current_stage'],
        ]);

        // Add progress update
        $order->addProgressUpdate(
            $validated['current_stage'],
            $validated['progress_note'] ?? "Progress updated from {$oldProgress}% to {$validated['progress_percentage']}%"
        );

        // Create notification for user
        Notification::create([
            'user_id' => $order->user_id,
            'type' => 'order_progress',
            'survey_booking_id' => $order->survey_booking_id,
            'title' => 'Update Progress Order',
            'message' => "Order {$order->order_number}: {$validated['current_stage']} ({$validated['progress_percentage']}%)",
            'data' => json_encode([
                'order_id' => $order->id,
                'progress_percentage' => $validated['progress_percentage'],
            ]),
        ]);

        return redirect()->back()->with('success', 'Progress order berhasil diupdate.');
    }

    /**
     * Start production
     */
    public function startProduction(Order $order)
    {
        if ($order->status !== 'dp_confirmed') {
            return redirect()->back()->with('error', 'Order tidak dalam status DP confirmed.');
        }

        $order->update([
            'status' => 'in_progress',
            'progress_percentage' => 10,
            'current_stage' => 'Produksi dimulai',
        ]);

        $order->addProgressUpdate('Produksi dimulai', 'Pekerjaan sudah dimulai');

        // Create notification
        Notification::create([
            'user_id' => $order->user_id,
            'type' => 'order_progress',
            'survey_booking_id' => $order->survey_booking_id,
            'title' => 'Produksi Dimulai',
            'message' => "Order {$order->order_number} telah memasuki tahap produksi.",
        ]);

        return redirect()->back()->with('success', 'Produksi berhasil dimulai.');
    }

    /**
     * Mark as ready for pickup
     */
    public function markReady(Order $order)
    {
        if ($order->status !== 'in_progress') {
            return redirect()->back()->with('error', 'Order tidak dalam status in progress.');
        }

        $order->update([
            'status' => 'ready_for_pickup',
            'progress_percentage' => 95,
            'current_stage' => 'Siap diambil',
        ]);

        $order->addProgressUpdate('Siap diambil', 'Pekerjaan sudah selesai, menunggu pelunasan dan pengambilan');

        // Create notification
        Notification::create([
            'user_id' => $order->user_id,
            'type' => 'order_progress',
            'survey_booking_id' => $order->survey_booking_id,
            'title' => 'Order Siap Diambil',
            'message' => "Order {$order->order_number} sudah selesai dan siap diambil. Silakan lakukan pelunasan.",
        ]);

        return redirect()->back()->with('success', 'Order ditandai siap diambil.');
    }

    /**
     * Mark as completed (after full payment)
     */
    public function markCompleted(Order $order)
    {
        if ($order->status !== 'ready_for_pickup') {
            return redirect()->back()->with('error', 'Order belum ready for pickup.');
        }

        // Check if fully paid
        if (!$order->isFullyPaid()) {
            return redirect()->back()->with('error', 'Order belum lunas.');
        }

        $order->update([
            'status' => 'completed',
            'progress_percentage' => 100,
            'current_stage' => 'Selesai',
            'completed_at' => now(),
        ]);

        $order->addProgressUpdate('Selesai', 'Order telah selesai dan diambil customer');

        // Create notification
        Notification::create([
            'user_id' => $order->user_id,
            'type' => 'order_progress',
            'survey_booking_id' => $order->survey_booking_id,
            'title' => 'Order Selesai',
            'message' => "Order {$order->order_number} telah selesai. Terima kasih!",
        ]);

        return redirect()->back()->with('success', 'Order berhasil diselesaikan.');
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, Order $order)
    {
        $validated = $request->validate([
            'cancel_reason' => 'required|string',
        ]);

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'current_stage' => 'Dibatalkan oleh admin',
        ]);

        $order->addProgressUpdate('Order dibatalkan', $validated['cancel_reason']);

        // Create notification
        Notification::create([
            'user_id' => $order->user_id,
            'type' => 'order_progress',
            'survey_booking_id' => $order->survey_booking_id,
            'title' => 'Order Dibatalkan',
            'message' => "Order {$order->order_number} dibatalkan. Alasan: {$validated['cancel_reason']}",
        ]);

        return redirect()->back()->with('success', 'Order berhasil dibatalkan.');
    }

    /**
     * All orders with filters
     */
    public function allOrders(Request $request)
    {
        $query = Order::with(['user', 'surveyBooking']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.all', compact('orders'));
    }
}
