@extends('layouts.app')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 to-blue-100">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-3">🔍 Lacak Order</h1>
            <p class="text-gray-600 text-lg">Cek status dan progress order Anda dengan nomor order</p>
        </div>

        <!-- Track Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <form action="{{ route('orders.track') }}" method="GET">
                <label for="order_number" class="block text-sm font-medium text-gray-700 mb-3">
                    Masukkan Nomor Order
                </label>
                <div class="flex space-x-3">
                    <input type="text" 
                        id="order_number" 
                        name="order_number" 
                        value="{{ request('order_number') }}"
                        placeholder="Contoh: ORD-20240324-ABCD"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                        required>
                    <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition duration-200">
                        🔍 Lacak
                    </button>
                </div>
            </form>
        </div>

        @if(request('order_number') && !isset($order))
            <!-- Not Found -->
            <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-r-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-red-900">Order Tidak Ditemukan</h3>
                        <p class="text-red-800 mt-1">Nomor order <strong>{{ request('order_number') }}</strong> tidak ditemukan. Pastikan nomor order benar.</p>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($order))
            <!-- Order Found -->
            <div class="space-y-6">
                <!-- Order Header -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div class="text-white">
                                <h2 class="text-2xl font-bold">{{ $order->order_number }}</h2>
                                <p class="opacity-90 mt-1">{{ $order->surveyBooking->project_type }}</p>
                            </div>
                            <span class="px-4 py-2 rounded-lg font-semibold {{ $order->status_badge['class'] }}">
                                {{ $order->status_badge['text'] }}
                            </span>
                        </div>
                    </div>

                    <div class="p-8">
                        <!-- Progress Bar -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700">Progress Pengerjaan</span>
                                <span class="text-lg font-bold text-blue-600">{{ $order->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-4 rounded-full transition-all duration-500" 
                                    style="width: {{ $order->progress_percentage }}%"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-3 font-medium">{{ $order->current_stage }}</p>
                        </div>

                        <!-- Order Details -->
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Customer</label>
                                <p class="text-gray-900 font-semibold">{{ $order->surveyBooking->user->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Total Harga</label>
                                <p class="text-gray-900 font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">DP Dibayar</label>
                                <p class="text-green-600 font-semibold">Rp {{ number_format($order->dp_paid, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Sisa Pembayaran</label>
                                <p class="text-red-600 font-semibold">Rp {{ number_format($order->remaining_amount - ($order->remaining_paid ?? 0), 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Timeline -->
                @if($order->progress_updates && count($order->progress_updates) > 0)
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">📋 Riwayat Progress</h3>
                        <div class="space-y-4">
                            @foreach(array_reverse($order->progress_updates) as $update)
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 bg-gray-50 rounded-lg p-4">
                                        <p class="text-sm font-bold text-gray-900">{{ $update['stage'] }}</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ $update['description'] }}</p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <p class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($update['updated_at'])->format('d M Y, H:i') }}
                                            </p>
                                            <span class="text-xs font-bold text-blue-600">{{ $update['percentage'] }}%</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Info Box -->
        @if(!isset($order))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8">
                <h3 class="font-semibold text-blue-900 mb-2">ℹ️ Cara Melacak Order</h3>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li>• Nomor order akan dikirim melalui email atau notifikasi setelah DP dikonfirmasi</li>
                    <li>• Masukkan nomor order lengkap (contoh: ORD-20240324-ABCD)</li>
                    <li>• Anda dapat melacak progress order kapan saja tanpa perlu login</li>
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
