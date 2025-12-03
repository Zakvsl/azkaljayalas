@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Print Button -->
        <div class="mb-4 flex justify-between items-center print:hidden">
            <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                ← Kembali ke Detail Order
            </a>
            <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                🖨️ Cetak Invoice
            </button>
        </div>

        <!-- Invoice Card -->
        <div class="bg-white rounded-lg shadow-lg p-8" id="invoice">
            <!-- Header -->
            <div class="border-b-2 border-gray-300 pb-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">INVOICE</h1>
                        <p class="text-gray-600 mt-1">{{ $order->order_number }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-600">AZKAL JAYA LAS</div>
                        <p class="text-sm text-gray-600 mt-2">Jasa Pengelasan Profesional</p>
                        <p class="text-sm text-gray-600">Bandung, Jawa Barat</p>
                        <p class="text-sm text-gray-600">WA: 0852-9267-4783</p>
                    </div>
                </div>
            </div>

            <!-- Invoice Info -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Kepada:</h3>
                    <div class="text-gray-800">
                        <p class="font-semibold">{{ $order->user->name }}</p>
                        <p class="text-sm">{{ $order->user->email }}</p>
                        <p class="text-sm">{{ $order->user->phone }}</p>
                        @if($order->surveyBooking)
                            <p class="text-sm mt-2">{{ $order->surveyBooking->address }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="space-y-2">
                        <div>
                            <span class="text-sm text-gray-600">Tanggal Invoice:</span>
                            <span class="font-semibold text-gray-800">{{ $order->completed_at?->format('d M Y') ?? now()->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Tanggal Order:</span>
                            <span class="font-semibold text-gray-800">{{ $order->created_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Status:</span>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">LUNAS</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Detail Pekerjaan</h3>
                <div class="space-y-2">
                    @if($order->surveyBooking && $order->surveyBooking->surveyResult)
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Jenis Material:</p>
                                <p class="font-semibold">{{ $order->surveyBooking->surveyResult->material_type }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Ketebalan Material:</p>
                                <p class="font-semibold">{{ $order->surveyBooking->surveyResult->thickness }} mm</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Teknik Pengelasan:</p>
                                <p class="font-semibold">{{ $order->surveyBooking->surveyResult->welding_technique }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Waktu Pengerjaan:</p>
                                <p class="font-semibold">{{ $order->surveyBooking->surveyResult->working_days }} hari</p>
                            </div>
                        </div>
                        @if($order->surveyBooking->surveyResult->project_notes)
                            <div class="mt-3">
                                <p class="text-sm text-gray-600">Catatan Proyek:</p>
                                <p class="text-gray-800 text-sm">{{ $order->surveyBooking->surveyResult->project_notes }}</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Payment Breakdown -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Rincian Pembayaran</h3>
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-sm font-semibold text-gray-700">Keterangan</th>
                            <th class="text-right py-2 text-sm font-semibold text-gray-700">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="py-3 text-gray-800">Total Harga Pekerjaan</td>
                            <td class="text-right font-semibold">Rp {{ number_format($order->final_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 text-gray-600">
                                Down Payment ({{ $order->dp_percentage }}%)
                                <span class="text-xs block text-gray-500">
                                    Dibayar: {{ $order->payments()->where('payment_type', 'dp')->first()?->created_at->format('d M Y') }}
                                </span>
                            </td>
                            <td class="text-right text-gray-800">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 text-gray-600">
                                Pelunasan
                                <span class="text-xs block text-gray-500">
                                    Dibayar: {{ $order->payments()->where('payment_type', 'remaining')->first()?->created_at->format('d M Y') }}
                                </span>
                            </td>
                            <td class="text-right text-gray-800">Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-blue-50">
                            <td class="py-4 font-bold text-gray-900">TOTAL DIBAYAR</td>
                            <td class="text-right font-bold text-xl text-blue-600">Rp {{ number_format($order->final_price, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Payment History -->
            @if($order->payments->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Riwayat Pembayaran</h3>
                    <div class="space-y-2">
                        @foreach($order->payments as $payment)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ $payment->payment_type === 'dp' ? 'Down Payment' : 'Pelunasan' }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $payment->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                    <p class="text-xs">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                            {{ $payment->status }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t-2 border-gray-300">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 mb-4">Catatan:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• Invoice ini sah tanpa tanda tangan dan stempel</li>
                            <li>• Simpan invoice ini sebagai bukti pembayaran</li>
                            <li>• Untuk pertanyaan, hubungi kami via WhatsApp</li>
                        </ul>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 mb-2">Bandung, {{ now()->format('d F Y') }}</p>
                        <div class="mt-12">
                            <p class="font-semibold text-gray-800 border-t border-gray-400 inline-block pt-1 px-8">
                                AZKAL JAYA LAS
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code / Verification (Optional) -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    Invoice ID: {{ $order->order_number }} | Dicetak: {{ now()->format('d M Y H:i') }}
                </p>
            </div>
        </div>

        <!-- WhatsApp Contact (Print Hidden) -->
        <div class="mt-6 text-center print:hidden">
            <p class="text-gray-600 mb-2">Ada pertanyaan tentang invoice ini?</p>
            <a href="https://wa.me/6285292674783?text=Halo, saya ingin bertanya tentang invoice {{ $order->order_number }}" 
                target="_blank"
                class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                💬 Hubungi via WhatsApp
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body {
            background: white;
        }
        .print\:hidden {
            display: none !important;
        }
        #invoice {
            box-shadow: none;
            border-radius: 0;
        }
    }
</style>
@endpush
@endsection
