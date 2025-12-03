@extends('layouts.app')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">💰 Pembayaran</h1>
            <p class="text-gray-600 mt-2">Kelola pembayaran DP dan pelunasan Anda</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <p class="text-sm font-medium text-yellow-800">Pending</p>
                <p class="text-3xl font-bold text-yellow-900">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <p class="text-sm font-medium text-blue-800">Menunggu Konfirmasi</p>
                <p class="text-3xl font-bold text-blue-900">{{ $stats['waiting'] }}</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <p class="text-sm font-medium text-green-800">Dikonfirmasi</p>
                <p class="text-3xl font-bold text-green-900">{{ $stats['confirmed'] }}</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <p class="text-sm font-medium text-red-800">Ditolak</p>
                <p class="text-3xl font-bold text-red-900">{{ $stats['rejected'] }}</p>
            </div>
        </div>

        <!-- Payments List -->
        @if($payments->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Pembayaran</h3>
                <p class="text-gray-500">Pembayaran akan muncul setelah Anda menerima penawaran harga</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $payment->surveyBooking->project_type }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $payment->surveyBooking->location }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($payment->payment_type === 'dp')
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                                DP ({{ $payment->dp_percentage }}%)
                                            </span>
                                        @elseif($payment->payment_type === 'remaining')
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                                                Pelunasan
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                                                Penuh
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($payment->payment_type === 'dp' ? $payment->dp_amount : $payment->total_price, 0, ',', '.') }}
                                        </div>
                                        @if($payment->payment_type === 'dp')
                                            <div class="text-xs text-gray-500">
                                                Total: Rp {{ number_format($payment->total_price, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($payment->status === 'pending')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Belum Dibayar
                                            </span>
                                        @elseif($payment->status === 'waiting_confirmation')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Menunggu Konfirmasi
                                            </span>
                                        @elseif($payment->status === 'confirmed')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Dikonfirmasi
                                            </span>
                                        @elseif($payment->status === 'rejected')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('payments.show', $payment) }}" 
                                            class="text-blue-600 hover:text-blue-900 font-medium">
                                            Detail
                                        </a>
                                        @if($payment->status === 'pending')
                                            <a href="{{ route('payments.create', ['payment_id' => $payment->id]) }}" 
                                                class="ml-3 text-green-600 hover:text-green-900 font-medium">
                                                Upload Bukti
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $payments->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
