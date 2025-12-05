<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        return view('admin.orders.create');
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'project_type' => 'required|string',
            'material_type' => 'required|string',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'thickness' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
            'actual_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending_dp,dp_pending_confirm,in_progress,ready_for_pickup,completed,cancelled',
            'notes' => 'nullable|string',
            'order_date' => 'required|date',
            'completion_date' => 'nullable|date|after_or_equal:order_date',
        ]);

        // Prepare dimensions
        $dimensions = [];
        if ($request->filled('length')) $dimensions['length'] = $request->length;
        if ($request->filled('width')) $dimensions['width'] = $request->width;
        if ($request->filled('height')) $dimensions['height'] = $request->height;
        if ($request->filled('thickness')) $dimensions['thickness'] = $request->thickness;

        $validated['dimensions'] = !empty($dimensions) ? $dimensions : null;

        Order::create($validated);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pesanan berhasil ditambahkan!');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'project_type' => 'required|string',
            'material_type' => 'required|string',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'thickness' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
            'actual_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending_dp,dp_pending_confirm,in_progress,ready_for_pickup,completed,cancelled',
            'notes' => 'nullable|string',
            'order_date' => 'required|date',
            'completion_date' => 'nullable|date|after_or_equal:order_date',
        ]);

        // Prepare dimensions
        $dimensions = [];
        if ($request->filled('length')) $dimensions['length'] = $request->length;
        if ($request->filled('width')) $dimensions['width'] = $request->width;
        if ($request->filled('height')) $dimensions['height'] = $request->height;
        if ($request->filled('thickness')) $dimensions['thickness'] = $request->thickness;

        $validated['dimensions'] = !empty($dimensions) ? $dimensions : null;

        $order->update($validated);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pesanan berhasil diperbarui!');
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pesanan berhasil dihapus!');
    }
}
