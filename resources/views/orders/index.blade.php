@extends('layouts.app')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">📦 Order Saya</h1>
            <p class="text-gray-600 mt-2">Pantau progress pengerjaan order Anda</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <p class="text-sm font-medium text-yellow-800">DP Confirmed</p>
                <p class="text-3xl font-bold text-yellow-900">{{ $stats['dp_confirmed'] }}</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <p class="text-sm font-medium text-blue-800">In Progress</p>
                <p class="text-3xl font-bold text-blue-900">{{ $stats['in_progress'] }}</p>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                <p class="text-sm font-medium text-purple-800">Ready</p>
                <p class="text-3xl font-bold text-purple-900">{{ $stats['ready_for_pickup'] }}</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <p class="text-sm font-medium text-green-800">Completed</p>
                <p class="text-3xl font-bold text-green-900">{{ $stats['completed'] }}</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <p class="text-sm font-medium text-red-800">Cancelled</p>
                <p class="text-3xl font-bold text-red-900">{{ $stats['cancelled'] }}</p>
            </div>
        </div>

        <!-- Orders List -->
        @if($orders->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Order</h3>
                <p class="text-gray-500">Order akan muncul setelah Anda membayar DP</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
                        <!-- Order Header -->
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="text-white">
                                    <h3 class="text-lg font-bold">{{ $order->order_number }}</h3>
                                    <p class="text-sm opacity-90">{{ $order->surveyBooking->project_type }}</p>
                                </div>
                                <span class="px-4 py-2 rounded-lg font-semibold {{ $order->status_badge['class'] }}">
                                    {{ $order->status_badge['text'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Order Body -->
                        <div class="p-6">
                            <!-- Progress Bar -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progress Pengerjaan</span>
                                    <span class="text-sm font-medium text-blue-600">{{ $order->progress_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" 
                                        style="width: {{ $order->progress_percentage }}%"></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">{{ $order->current_stage }}</p>
                            </div>

                            <!-- Order Details -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                <div>
                                    <p class="text-xs text-gray-500">Total Harga</p>
                                    <p class="font-semibold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">DP Dibayar</p>
                                    <p class="font-semibold text-green-600">Rp {{ number_format($order->dp_paid, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Sisa Pembayaran</p>
                                    <p class="font-semibold text-red-600">Rp {{ number_format($order->remaining_amount - ($order->remaining_paid ?? 0), 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Tanggal Order</p>
                                    <p class="font-semibold text-gray-900">{{ $order->created_at->format('d M Y') }}</p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <a href="{{ route('orders.show', $order) }}" 
                                    class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    Lihat Detail →
                                </a>
                                @if($order->status === 'dp_confirmed')
                                    <form action="{{ route('orders.cancel', $order) }}" method="POST" 
                                        onsubmit="return confirm('Yakin ingin membatalkan order ini?')">
                                        @csrf
                                        <button type="submit" 
                                            class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            Batalkan Order
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
