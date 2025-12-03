<div class="space-y-4">
    @foreach($data as $payment)
        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $payment->surveyBooking->project_type ?? 'Proyek' }}</h3>
                    <p class="text-sm text-gray-600 mt-1">Payment ID: #{{ $payment->id }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $payment->status === 'pending' ? 'Menunggu Persetujuan' : 'Menunggu Konfirmasi' }}
                </span>
            </div>

            <div class="bg-yellow-50 rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">Penawaran Harga</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Total Harga</p>
                        <p class="text-xl font-bold text-gray-900">Rp {{ number_format($payment->total_price, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">DP ({{ $payment->dp_percentage }}%)</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($payment->dp_amount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                @if($payment->status === 'pending')
                    <a href="{{ route('survey-booking.price-offer', $payment->survey_booking_id) }}" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm">
                        Lihat Penawaran
                    </a>
                @else
                    <a href="{{ route('payments.show', $payment) }}" 
                        class="text-yellow-600 hover:text-yellow-800 font-medium text-sm flex items-center gap-1">
                        Lihat Detail
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</div>
