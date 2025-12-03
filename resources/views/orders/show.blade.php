@extends('layouts.app')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-start">
            <div>
                <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar Order
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">📦 Detail Order</h1>
            </div>
            @if($order->status === 'completed')
                <a href="{{ route('orders.invoice', $order) }}" 
                    class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all inline-flex items-center gap-2">
                    📄 Lihat Invoice
                </a>
            @endif
        </div>

        <!-- Order Header -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $order->order_number }}</h2>
                    <p class="text-gray-600">Dibuat {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <div>
                    <span class="px-4 py-2 rounded-lg font-semibold {{ $order->status_badge['class'] }}">
                        {{ $order->status_badge['text'] }}
                    </span>
                </div>
            </div>

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

            <!-- Order Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500">Proyek</label>
                    <p class="text-gray-900 font-semibold">{{ $order->surveyBooking->project_type }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Total Harga</label>
                    <p class="text-gray-900 font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">DP Dibayar</label>
                    <p class="text-gray-900">Rp {{ number_format($order->dp_paid, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Sisa Pembayaran</label>
                    <p class="text-gray-900 font-semibold text-red-600">
                        Rp {{ number_format($order->remaining_amount - ($order->remaining_paid ?? 0), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Progress Updates -->
        @if($order->progress_updates && count($order->progress_updates) > 0)
            <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Riwayat Progress</h3>
                <div class="space-y-4">
                    @foreach(array_reverse($order->progress_updates) as $update)
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $update['stage'] }}</p>
                                <p class="text-sm text-gray-600">{{ $update['description'] }}</p>
                                <div class="flex items-center mt-1 space-x-4">
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($update['updated_at'])->format('d M Y, H:i') }}
                                    </p>
                                    <span class="text-xs font-medium text-blue-600">{{ $update['percentage'] }}%</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Payment Info -->
        @if($order->payments && $order->payments->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Riwayat Pembayaran</h3>
                <div class="space-y-4">
                    @foreach($order->payments as $payment)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-900">
                                    @if($payment->payment_type === 'dp')
                                        Pembayaran DP ({{ $payment->dp_percentage }}%)
                                    @else
                                        Pelunasan
                                    @endif
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $payment->status_badge['class'] }}">
                                    {{ $payment->status_badge['text'] }}
                                </span>
                            </div>
                            <p class="text-lg font-bold text-gray-900">
                                Rp {{ number_format($payment->paid_amount, 0, ',', '.') }}
                            </p>
                            @if($payment->paid_at)
                                <p class="text-sm text-gray-600 mt-1">
                                    Dibayar {{ $payment->paid_at->format('d M Y, H:i') }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
