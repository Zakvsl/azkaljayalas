@extends('layouts.app')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('payments.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Pembayaran
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-4">💰 Detail Pembayaran</h1>
        </div>

        <!-- Status Alert -->
        @if($payment->status === 'waiting_confirmation')
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6 rounded-r-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-blue-900">Menunggu Konfirmasi</h3>
                        <p class="text-blue-800 mt-1">Pembayaran Anda sedang diverifikasi oleh admin. Proses ini biasanya memakan waktu 1x24 jam.</p>
                    </div>
                </div>
            </div>
        @elseif($payment->status === 'confirmed')
            <div class="bg-green-50 border-l-4 border-green-500 p-6 mb-6 rounded-r-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-green-900">Pembayaran Dikonfirmasi</h3>
                        <p class="text-green-800 mt-1">Pembayaran Anda telah dikonfirmasi pada {{ $payment->confirmed_at?->format('d M Y, H:i') }} WIB</p>
                    </div>
                </div>
            </div>
        @elseif($payment->status === 'rejected')
            <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-6 rounded-r-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-red-900">Pembayaran Ditolak</h3>
                        <p class="text-red-800 mt-1">{{ $payment->admin_notes ?? 'Pembayaran ditolak. Silakan hubungi admin untuk informasi lebih lanjut.' }}</p>
                    </div>
                </div>
            </div>
        @elseif($payment->status === 'pending')
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 mb-6 rounded-r-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-yellow-900">Belum Dibayar</h3>
                        <p class="text-yellow-800 mt-1">Silakan upload bukti pembayaran Anda.</p>
                        <a href="{{ route('payments.create', ['payment_id' => $payment->id]) }}" 
                            class="mt-3 inline-block px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-medium">
                            Upload Bukti Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Payment Details -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Detail Pembayaran</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500">Proyek</label>
                    <p class="text-gray-900 font-semibold">{{ $payment->surveyBooking->project_type }}</p>
                    <p class="text-sm text-gray-600">{{ $payment->surveyBooking->location }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Tipe Pembayaran</label>
                    <p class="text-gray-900 font-semibold">
                        @if($payment->payment_type === 'dp')
                            DP ({{ $payment->dp_percentage }}%)
                        @elseif($payment->payment_type === 'remaining')
                            Pelunasan
                        @else
                            Pembayaran Penuh
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Total Harga Proyek</label>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($payment->total_price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Jumlah yang Dibayar</label>
                    <p class="text-2xl font-bold text-blue-600">
                        Rp {{ number_format($payment->paid_amount ?? ($payment->payment_type === 'dp' ? $payment->dp_amount : $payment->remaining_amount), 0, ',', '.') }}
                    </p>
                </div>
                @if($payment->payment_method)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Metode Pembayaran</label>
                        <p class="text-gray-900">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</p>
                    </div>
                @endif
                @if($payment->paid_at)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tanggal Pembayaran</label>
                        <p class="text-gray-900">{{ $payment->paid_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                @endif
            </div>

            @if($payment->payment_notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <label class="text-sm font-medium text-gray-500">Catatan</label>
                    <p class="text-gray-900 mt-1">{{ $payment->payment_notes }}</p>
                </div>
            @endif
        </div>

        <!-- Payment Proof -->
        @if($payment->payment_proof)
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Bukti Pembayaran</h2>
                <div class="flex justify-center">
                    <img src="{{ Storage::url($payment->payment_proof) }}" 
                        alt="Bukti Pembayaran" 
                        class="max-w-full h-auto rounded-lg shadow-lg border border-gray-200"
                        style="max-height: 600px;">
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
