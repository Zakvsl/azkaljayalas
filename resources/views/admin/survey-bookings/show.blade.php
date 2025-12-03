@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Detail Booking Survei</h2>
            <a href="{{ route('admin.survey-bookings.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                Kembali
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <!-- Status Update -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="px-4 py-2 rounded-full text-sm font-semibold
                            {{ $surveyBooking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $surveyBooking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $surveyBooking->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $surveyBooking->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($surveyBooking->status) }}
                        </span>
                    </div>
                    
                    @if($surveyBooking->status !== 'cancelled')
                    <div class="flex gap-3">
                        @if($surveyBooking->status === 'pending')
                        <form action="{{ route('admin.survey-bookings.confirm', $surveyBooking) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Konfirmasi Booking
                            </button>
                        </form>
                        @endif
                        
                        <button type="button" 
                                onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Batalkan Booking
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <div class="p-6">
                <!-- Customer Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Customer</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Nama</label>
                            <p class="text-gray-900">{{ $surveyBooking->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Email</label>
                            <p class="text-gray-900">{{ $surveyBooking->user->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">No. Telepon</label>
                            <p class="text-gray-900">{{ $surveyBooking->user->phone_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Alamat Customer</label>
                            <p class="text-gray-900">{{ $surveyBooking->user->address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Project Information -->
                <div class="mb-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Proyek</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tipe Proyek</label>
                            <p class="text-gray-900">{{ ucfirst($surveyBooking->project_type) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tanggal yang Diinginkan</label>
                            <p class="text-gray-900">{{ $surveyBooking->preferred_date->format('d F Y') }}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-sm font-medium text-gray-600">Deskripsi Proyek</label>
                            <p class="text-gray-900 whitespace-pre-line">{{ $surveyBooking->project_description }}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-sm font-medium text-gray-600">Lokasi Survei</label>
                            <p class="text-gray-900 whitespace-pre-line mb-3">{{ $surveyBooking->location }}</p>
                            
                            @if($surveyBooking->latitude && $surveyBooking->longitude)
                            <div class="mt-3">
                                <div id="map" class="w-full h-64 rounded-lg border border-gray-300"></div>
                                <p class="text-xs text-gray-500 mt-2">
                                    Koordinat: {{ $surveyBooking->latitude }}, {{ $surveyBooking->longitude }}
                                </p>
                            </div>
                            @endif
                        </div>
                        @if($surveyBooking->notes)
                        <div class="col-span-2">
                            <label class="text-sm font-medium text-gray-600">Catatan Tambahan</label>
                            <p class="text-gray-900 whitespace-pre-line">{{ $surveyBooking->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Price Estimate Information (if exists) -->
                @if($surveyBooking->priceEstimate)
                <div class="mb-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Estimasi Harga</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Estimasi Harga</label>
                            <p class="text-gray-900 font-semibold">Rp {{ number_format($surveyBooking->priceEstimate->estimated_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tanggal Estimasi</label>
                            <p class="text-gray-900">{{ $surveyBooking->priceEstimate->created_at->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Timestamps -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <label class="font-medium">Tanggal Dibuat</label>
                            <p>{{ $surveyBooking->created_at->format('d F Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="font-medium">Terakhir Diupdate</label>
                            <p>{{ $surveyBooking->updated_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Batalkan Booking Survey</h3>
                <button onclick="document.getElementById('cancelModal').classList.add('hidden')" 
                        class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('admin.survey-bookings.cancel', $surveyBooking) }}" method="POST">
                @csrf
                
                @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <div class="mb-4">
                    <label for="cancel_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Pembatalan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="cancel_reason" 
                              name="cancel_reason" 
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 @error('cancel_reason') border-red-500 @enderror"
                              placeholder="Jelaskan alasan pembatalan booking ini...">{{ old('cancel_reason') }}</textarea>
                    @error('cancel_reason')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Alasan ini akan dikirim ke customer melalui notifikasi</p>
                </div>
                
                <div class="flex gap-3 justify-end">
                    <button type="button" 
                            onclick="document.getElementById('cancelModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Ya, Batalkan Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    // Auto-open modal if validation errors exist
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('cancelModal').classList.remove('hidden');
    });
</script>
@endif

@if($surveyBooking->latitude && $surveyBooking->longitude)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        const lat = {{ $surveyBooking->latitude }};
        const lng = {{ $surveyBooking->longitude }};
        
        const map = L.map('map').setView([lat, lng], 15);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Add marker for the location
        const marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup(`
            <div class="text-center">
                <p class="font-semibold text-sm">Lokasi Survei</p>
                <p class="text-xs text-gray-600 mt-1">{{ $surveyBooking->location }}</p>
            </div>
        `).openPopup();
        
        // Add circle to show area
        L.circle([lat, lng], {
            color: '#3B82F6',
            fillColor: '#93C5FD',
            fillOpacity: 0.2,
            radius: 100
        }).addTo(map);
    });
</script>
@endif

@endsection
