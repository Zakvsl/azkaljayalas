<div class="space-y-4">
    @foreach($data as $booking)
        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $booking->project_type }}</h3>
                    <p class="text-sm text-gray-600 mt-1">Survey ID: #{{ $booking->id }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                    Survey Selesai
                </span>
            </div>

            @if($booking->surveyResult)
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Hasil Survey</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Material</p>
                            <p class="font-medium">{{ $booking->surveyResult->material_type }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Ketebalan</p>
                            <p class="font-medium">{{ $booking->surveyResult->thickness }} mm</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Durasi Pengerjaan</p>
                            <p class="font-medium">{{ $booking->surveyResult->working_days }} hari</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('survey-booking.show', $booking) }}" 
                    class="text-green-600 hover:text-green-800 font-medium text-sm flex items-center gap-1">
                    Lihat Detail
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    @endforeach
</div>
