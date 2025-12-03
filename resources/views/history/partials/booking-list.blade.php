<div class="space-y-4">
    @foreach($data as $booking)
        <div class="border border-gray-200 rounded-lg overflow-hidden" x-data="{ open: false }">
            <div class="p-6 hover:bg-gray-50 cursor-pointer" @click="open = !open">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $booking->project_type }}</h3>
                        <p class="text-sm text-gray-600 mt-1">Booking ID: #{{ $booking->id }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Tanggal Survey</p>
                        <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($booking->preferred_date)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Lokasi</p>
                        <p class="font-medium text-gray-800 truncate">{{ Str::limit($booking->location, 30) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Dibuat</p>
                        <p class="font-medium text-gray-800">{{ $booking->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Klik untuk detail</p>
                    </div>
                </div>
            </div>

            <!-- Detail Accordion -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="border-t border-gray-200 bg-gray-50 p-6"
                 style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Detail Proyek
                        </h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-xs text-gray-500">Jenis Proyek</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ ucfirst($booking->project_type) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Deskripsi</dt>
                                <dd class="text-sm text-gray-900">{{ $booking->project_description }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Lokasi Lengkap</dt>
                                <dd class="text-sm text-gray-900">{{ $booking->location }}</dd>
                            </div>
                            @if($booking->notes)
                            <div>
                                <dt class="text-xs text-gray-500">Catatan</dt>
                                <dd class="text-sm text-gray-900">{{ $booking->notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Jadwal & Status
                        </h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-xs text-gray-500">Tanggal Dibuat</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $booking->created_at->format('d M Y, H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Tanggal Survey Diinginkan</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($booking->preferred_date)->format('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Status Saat Ini</dt>
                                <dd class="text-sm">
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </dd>
                            </div>
                            @if($booking->priceEstimate)
                            <div>
                                <dt class="text-xs text-gray-500">Estimasi Harga Awal</dt>
                                <dd class="text-sm font-bold text-green-600">Rp {{ number_format($booking->priceEstimate->estimated_price, 0, ',', '.') }}</dd>
                            </div>
                            @endif
                        </dl>

                        @if($booking->status === 'pending')
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg flex items-start gap-2">
                            <svg class="w-4 h-4 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs text-yellow-800">Menunggu konfirmasi admin</p>
                        </div>
                        @endif

                        @if($booking->status === 'cancelled' && $booking->cancel_reason)
                        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start gap-2 mb-2">
                                <svg class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs font-semibold text-red-800">Alasan Pembatalan:</p>
                            </div>
                            <p class="text-xs text-red-700 ml-6">{{ $booking->cancel_reason }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
