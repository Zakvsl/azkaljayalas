<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SurveyBooking;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Display a listing of user payments
     */
    public function index()
    {
        $payments = Payment::with(['surveyBooking', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'pending' => Payment::where('user_id', Auth::id())->where('status', 'pending')->count(),
            'waiting' => Payment::where('user_id', Auth::id())->where('status', 'waiting_confirmation')->count(),
            'confirmed' => Payment::where('user_id', Auth::id())->where('status', 'confirmed')->count(),
            'rejected' => Payment::where('user_id', Auth::id())->where('status', 'rejected')->count(),
        ];

        return view('payments.index', compact('payments', 'stats'));
    }

    /**
     * Show payment form for DP
     */
    public function create(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $payment = Payment::findOrFail($paymentId);

        // Check authorization
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if already uploaded
        if ($payment->status !== 'pending') {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Pembayaran ini sudah diupload.');
        }

        return view('payments.create', compact('payment'));
    }

    /**
     * Upload payment proof
     */
    public function store(Request $request, Payment $payment)
    {
        // Check authorization
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        // Check status
        if ($payment->status !== 'pending') {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Pembayaran ini sudah diupload.');
        }

        $validated = $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:transfer_bca,transfer_mandiri,transfer_bri,transfer_bni,cash',
            'payment_notes' => 'nullable|string',
        ]);

        // Upload payment proof
        $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

        // Update payment
        $payment->update([
            'payment_proof' => $proofPath,
            'paid_amount' => $validated['paid_amount'],
            'payment_method' => $validated['payment_method'],
            'payment_notes' => $validated['payment_notes'],
            'paid_at' => now(),
            'status' => 'waiting_confirmation',
        ]);

        // Create notification for user
        Notification::create([
            'user_id' => Auth::id(),
            'type' => 'payment',
            'survey_booking_id' => $payment->survey_booking_id,
            'title' => 'Bukti Pembayaran Berhasil Diupload',
            'message' => "Bukti pembayaran DP Rp " . number_format($validated['paid_amount'], 0, ',', '.') . " berhasil diupload. Menunggu konfirmasi admin.",
        ]);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Bukti pembayaran berhasil diupload. Admin akan segera mengkonfirmasi.');
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        // Check authorization
        if ($payment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $payment->load(['surveyBooking', 'user', 'order']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Upload remaining payment
     */
    public function uploadRemaining(Request $request, Payment $payment)
    {
        // Check authorization
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if DP is confirmed
        if ($payment->payment_type === 'dp' && $payment->status !== 'confirmed') {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'DP harus dikonfirmasi terlebih dahulu.');
        }

        $validated = $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:transfer_bca,transfer_mandiri,transfer_bri,transfer_bni,cash',
            'payment_notes' => 'nullable|string',
        ]);

        // Upload payment proof
        $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

        // Create new payment record for remaining
        $remainingPayment = Payment::create([
            'survey_booking_id' => $payment->survey_booking_id,
            'user_id' => Auth::id(),
            'payment_type' => 'remaining',
            'total_price' => $payment->total_price,
            'dp_amount' => $payment->dp_amount,
            'remaining_amount' => $payment->remaining_amount,
            'paid_amount' => $validated['paid_amount'],
            'payment_proof' => $proofPath,
            'payment_method' => $validated['payment_method'],
            'payment_notes' => $validated['payment_notes'],
            'paid_at' => now(),
            'status' => 'waiting_confirmation',
        ]);

        // Create notification
        Notification::create([
            'user_id' => Auth::id(),
            'type' => 'payment',
            'survey_booking_id' => $payment->survey_booking_id,
            'title' => 'Bukti Pelunasan Berhasil Diupload',
            'message' => "Bukti pelunasan Rp " . number_format($validated['paid_amount'], 0, ',', '.') . " berhasil diupload. Menunggu konfirmasi admin.",
        ]);

        return redirect()->route('payments.show', $remainingPayment)
            ->with('success', 'Bukti pelunasan berhasil diupload. Admin akan segera mengkonfirmasi.');
    }

    /**
     * Payment history with filters
     */
    public function history(Request $request)
    {
        $query = Payment::with(['surveyBooking'])
            ->where('user_id', Auth::id());

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by payment type
        if ($request->has('payment_type') && $request->payment_type !== '') {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('payments.history', compact('payments'));
    }
}
