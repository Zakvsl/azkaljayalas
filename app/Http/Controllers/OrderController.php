<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of user orders
     */
    public function index()
    {
        $orders = Order::with(['surveyBooking', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'active' => Order::where('user_id', Auth::id())
                ->whereIn('status', ['dp_confirmed', 'in_progress', 'ready_for_pickup'])
                ->count(),
            'completed' => Order::where('user_id', Auth::id())
                ->where('status', 'completed')
                ->count(),
            'cancelled' => Order::where('user_id', Auth::id())
                ->where('status', 'cancelled')
                ->count(),
        ];

        return view('orders.index', compact('orders', 'stats'));
    }

    /**
     * Show order details
     */
    public function show(Order $order)
    {
        // Check authorization
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $order->load(['surveyBooking', 'user', 'payments']);

        return view('orders.show', compact('order'));
    }

    /**
     * Track order progress (public page with order number)
     */
    public function track(Request $request)
    {
        $orderNumber = $request->query('order_number');
        
        if (!$orderNumber) {
            return view('orders.track-form');
        }

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return view('orders.track-form')
                ->with('error', 'Order tidak ditemukan. Periksa kembali nomor order Anda.');
        }

        $order->load(['surveyBooking', 'user']);

        return view('orders.track', compact('order'));
    }

    /**
     * Order history with filters
     */
    public function history(Request $request)
    {
        $query = Order::with(['surveyBooking'])
            ->where('user_id', Auth::id());

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('orders.history', compact('orders'));
    }

    /**
     * Cancel order (only if not started)
     */
    public function cancel(Request $request, Order $order)
    {
        // Check authorization
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow cancel if status is dp_confirmed
        if ($order->status !== 'dp_confirmed') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Order yang sudah dalam produksi tidak dapat dibatalkan.');
        }

        $validated = $request->validate([
            'cancel_reason' => 'required|string',
        ]);

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'current_stage' => 'Dibatalkan oleh customer',
        ]);

        $order->addProgressUpdate('Order dibatalkan', $validated['cancel_reason']);

        // Create notification
        Notification::create([
            'user_id' => Auth::id(),
            'type' => 'order_progress',
            'survey_booking_id' => $order->survey_booking_id,
            'title' => 'Order Dibatalkan',
            'message' => "Order {$order->order_number} telah dibatalkan.",
        ]);

        return redirect()->route('orders.index')
            ->with('success', 'Order berhasil dibatalkan.');
    }

    /**
     * Show invoice for completed order
     */
    public function invoice(Order $order)
    {
        // Check authorization
        if ($order->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Only show invoice for completed orders
        if ($order->status !== 'completed') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Invoice hanya tersedia untuk order yang sudah selesai.');
        }

        $order->load(['surveyBooking', 'user', 'payments']);

        return view('orders.invoice', compact('order'));
    }
}
