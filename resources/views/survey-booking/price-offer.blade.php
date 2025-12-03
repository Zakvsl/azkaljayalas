@extends('layouts.app')
@include('components.navbar')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">💰 Penawaran Harga Final</h1>
            <p class="text-gray-600 mt-2">Review dan putuskan penawaran harga dari admin</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Booking Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">📋 Info Proyek</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-gray-600 font-medium">Tipe Proyek:</span>
                    <p class="text-gray-800">{{ $booking->project_type }}</p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Lokasi:</span>
                    <p class="text-gray-800">{{ $booking->location }}</p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Tanggal Survei:</span>
                    <p class="text-gray-800">{{ \Carbon\Carbon::parse($booking->preferred_date)->format('d M Y') }} - {{ $booking->preferred_time }}</p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Status:</span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $booking->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>
        </div>

        @if($surveyResult)
        <!-- Survey Result Details -->
        <div class="bg-blue-50 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-blue-900">🔍 Detail Hasil Survei</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="text-blue-700 font-medium">Produk:</span>
                    <p class="text-blue-900">{{ $surveyResult->product->name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-blue-700 font-medium">Material:</span>
                    <p class="text-blue-900">{{ $surveyResult->material->name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-blue-700 font-medium">Finishing:</span>
                    <p class="text-blue-900">{{ $surveyResult->finishing->name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-blue-700 font-medium">Kerumitan:</span>
                    <p class="text-blue-900">{{ $surveyResult->kerumitan->name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-blue-700 font-medium">Ketebalan:</span>
                    <p class="text-blue-900">{{ $surveyResult->ketebalan->thickness ?? '-' }} mm</p>
                </div>
                <div>
                    <span class="text-blue-700 font-medium">Dimensi:</span>
                    <p class="text-blue-900">{{ $surveyResult->width }} x {{ $surveyResult->height }} 
                        @if($surveyResult->length > 0)
                            x {{ $surveyResult->length }}
                        @endif
                        cm
                    </p>
                </div>
                <div>
                    <span class="text-blue-700 font-medium">Quantity:</span>
                    <p class="text-blue-900">{{ $surveyResult->quantity }} unit</p>
                </div>
            </div>

            @if($surveyResult->survey_notes)
            <div class="mt-4 pt-4 border-t border-blue-200">
                <span class="text-blue-700 font-medium">Catatan Survei:</span>
                <p class="text-blue-900 mt-1">{{ $surveyResult->survey_notes }}</p>
            </div>
            @endif
        </div>

        <!-- Price Comparison -->
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-6 text-purple-900">💵 Rincian Harga</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- AI Estimation -->
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="flex items-center mb-2">
                        <span class="text-2xl mr-2">🤖</span>
                        <h3 class="font-semibold text-gray-700">Estimasi AI</h3>
                    </div>
                    <p class="text-3xl font-bold text-purple-600">
                        Rp {{ number_format($surveyResult->ai_estimated_price ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Estimasi otomatis dari ML</p>
                </div>

                <!-- Final Price -->
                <div class="bg-gradient-to-br from-green-100 to-green-200 rounded-lg p-4 shadow-lg border-2 border-green-400">
                    <div class="flex items-center mb-2">
                        <span class="text-2xl mr-2">✅</span>
                        <h3 class="font-semibold text-green-900">Harga Final</h3>
                    </div>
                    <p class="text-3xl font-bold text-green-700">
                        Rp {{ number_format($surveyResult->final_price ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-green-800 mt-1">Harga yang ditawarkan admin</p>
                </div>
            </div>

            @if($surveyResult->ai_estimated_price != $surveyResult->final_price)
            <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <span class="text-yellow-600 text-xl mr-2">💡</span>
                    <div>
                        <p class="font-semibold text-yellow-900">Catatan Penyesuaian Harga:</p>
                        <p class="text-sm text-yellow-800 mt-1">
                            Admin telah menyesuaikan harga berdasarkan kondisi lapangan dan kompleksitas proyek. 
                            @if($surveyResult->final_price > $surveyResult->ai_estimated_price)
                                Harga <strong>lebih tinggi</strong> dari estimasi AI sebesar Rp {{ number_format($surveyResult->final_price - $surveyResult->ai_estimated_price, 0, ',', '.') }}.
                            @else
                                Anda mendapat <strong>diskon</strong> sebesar Rp {{ number_format($surveyResult->ai_estimated_price - $surveyResult->final_price, 0, ',', '.') }}.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        @if($payment)
        <!-- Payment Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">💳 Detail Pembayaran</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-blue-700 font-medium">Total Harga</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1">
                        Rp {{ number_format($payment->total_price, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-green-700 font-medium">DP ({{ $payment->dp_percentage }}%)</p>
                    <p class="text-2xl font-bold text-green-900 mt-1">
                        Rp {{ number_format($payment->dp_amount, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-green-700 mt-1">Yang harus dibayar sekarang</p>
                </div>
                <div class="bg-orange-50 rounded-lg p-4">
                    <p class="text-sm text-orange-700 font-medium">Sisa Pembayaran</p>
                    <p class="text-2xl font-bold text-orange-900 mt-1">
                        Rp {{ number_format($payment->remaining_amount, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-orange-700 mt-1">Dibayar setelah proyek selesai</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        @if($payment->status === 'pending')
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">🎯 Keputusan Anda</h2>
            <p class="text-gray-600 mb-6">Apakah Anda menyetujui penawaran harga di atas?</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Accept Button -->
                <form action="{{ route('survey-booking.accept-price', $booking) }}" method="POST">
                    @csrf
                    <button 
                        type="submit" 
                        class="w-full px-6 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold shadow-lg flex items-center justify-center"
                        onclick="return confirm('Anda yakin menyetujui penawaran ini? Anda akan diarahkan untuk upload bukti pembayaran DP.')">
                        <span class="text-2xl mr-2">✅</span>
                        Setuju & Lanjut ke Pembayaran
                    </button>
                </form>

                <!-- Reject Button -->
                <button 
                    type="button" 
                    onclick="showRejectForm()"
                    class="w-full px-6 py-4 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition font-semibold border-2 border-red-300 flex items-center justify-center">
                    <span class="text-2xl mr-2">❌</span>
                    Tidak Setuju
                </button>
            </div>

            <!-- Reject Form (Hidden) -->
            <div id="rejectForm" class="hidden mt-6 bg-red-50 border border-red-300 rounded-lg p-6">
                <form action="{{ route('survey-booking.reject-price', $booking) }}" method="POST">
                    @csrf
                    <h3 class="font-semibold text-red-900 mb-3">Alasan Penolakan</h3>
                    <textarea 
                        name="reject_reason" 
                        rows="4" 
                        required
                        class="w-full border border-red-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Jelaskan alasan Anda menolak penawaran ini (opsional tapi disarankan)..."></textarea>
                    
                    <div class="flex justify-end space-x-3 mt-4">
                        <button 
                            type="button" 
                            onclick="hideRejectForm()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                            onclick="return confirm('Anda yakin ingin menolak penawaran ini? Booking akan dibatalkan.')">
                            Kirim Penolakan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="bg-gray-100 border border-gray-300 rounded-lg p-6">
            <p class="text-gray-700 text-center">
                @if($payment->status === 'waiting_confirmation')
                    ✅ Anda telah menyetujui penawaran dan upload bukti pembayaran. Menunggu konfirmasi admin.
                @elseif($payment->status === 'confirmed')
                    🎉 Pembayaran DP Anda telah dikonfirmasi! Proyek akan segera dimulai.
                @elseif($payment->status === 'rejected')
                    ❌ Pembayaran Anda ditolak. Silakan upload ulang atau hubungi admin.
                @else
                    ℹ️ Status: {{ $payment->status }}
                @endif
            </p>
        </div>
        @endif
        @endif

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-300 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 mb-2">ℹ️ Informasi Penting:</h3>
            <ul class="list-disc list-inside text-sm text-blue-800 space-y-1">
                <li>Jika Anda <strong>menyetujui</strong>, Anda akan diarahkan untuk upload bukti transfer DP</li>
                <li>Setelah DP dikonfirmasi, pekerjaan akan dimulai sesuai jadwal</li>
                <li>Sisa pembayaran dibayar setelah pekerjaan selesai</li>
                <li>Jika <strong>menolak</strong>, booking akan dibatalkan dan admin akan menerima notifikasi</li>
                <li>Anda dapat menghubungi admin via WhatsApp untuk negosiasi harga</li>
            </ul>
        </div>

        <!-- WhatsApp Contact -->
        <div class="mt-4 text-center">
            <a 
                href="https://wa.me/6285292674783?text=Halo%20admin%2C%20saya%20ingin%20diskusi%20mengenai%20penawaran%20harga%20untuk%20booking%20{{ $booking->id }}"
                target="_blank"
                class="inline-flex items-center px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Hubungi Admin via WhatsApp
            </a>
        </div>
    </div>
</div>

<script>
function showRejectForm() {
    document.getElementById('rejectForm').classList.remove('hidden');
}

function hideRejectForm() {
    document.getElementById('rejectForm').classList.add('hidden');
}
</script>
@endsection
