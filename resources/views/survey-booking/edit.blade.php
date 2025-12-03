@extends('layouts.app')
@include('components.navbar')
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 400px;
        border-radius: 0.5rem;
        z-index: 1;
    }
    .leaflet-container {
        font-family: 'Poppins', sans-serif;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('survey-booking.show', $surveyBooking) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Detail Booking
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-4">Edit Booking Survei</h1>
            <p class="text-gray-600 mt-2">Perbarui informasi booking survei Anda</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form action="{{ route('survey-booking.update', $surveyBooking) }}" method="POST" id="bookingForm">
                @csrf
                @method('PUT')

                <!-- Project Type -->
                <div class="mb-6">
                    <label for="project_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Proyek <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="project_type" 
                        name="project_type" 
                        value="{{ old('project_type', $surveyBooking->project_type) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('project_type') border-red-500 @enderror"
                        placeholder="Contoh: Pagar Besi, Kanopi, Railing Tangga"
                        required>
                    @error('project_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Project Description -->
                <div class="mb-6">
                    <label for="project_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Proyek <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="project_description" 
                        name="project_description" 
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('project_description') border-red-500 @enderror"
                        placeholder="Jelaskan detail proyek Anda, ukuran estimasi, bahan yang diinginkan, dll."
                        required>{{ old('project_description', $surveyBooking->project_description) }}</textarea>
                    @error('project_description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div class="mb-6">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Lokasi <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="location" 
                        name="location" 
                        rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                        placeholder="Masukkan alamat lengkap lokasi proyek"
                        required>{{ old('location', $surveyBooking->location) }}</textarea>
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WhatsApp Number -->
                <div class="mb-6">
                    <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <span class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">+62</span>
                        <input type="tel" 
                            id="whatsapp_number" 
                            name="whatsapp_number" 
                            value="{{ old('whatsapp_number', $surveyBooking->whatsapp_number) }}"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('whatsapp_number') border-red-500 @enderror"
                            placeholder="Contoh: 8123456789"
                            pattern="[0-9]{9,13}"
                            required>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Admin akan menghubungi Anda via WhatsApp untuk konfirmasi</p>
                    @error('whatsapp_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Map -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        📍 Pilih Lokasi di Peta (Opsional)
                    </label>
                    <p class="text-xs text-gray-600 mb-3">Klik pada peta untuk menandai lokasi proyek Anda</p>
                    <div id="map"></div>
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $surveyBooking->latitude) }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $surveyBooking->longitude) }}">
                    <p class="text-xs text-gray-500 mt-2" id="coordsDisplay">
                        Koordinat: <span id="coordsText">{{ $surveyBooking->latitude && $surveyBooking->longitude ? number_format($surveyBooking->latitude, 6) . ', ' . number_format($surveyBooking->longitude, 6) : 'Belum dipilih' }}</span>
                    </p>
                </div>

                <!-- Date & Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="preferred_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Survei <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                            id="preferred_date" 
                            name="preferred_date" 
                            value="{{ old('preferred_date', $surveyBooking->preferred_date?->format('Y-m-d')) }}"
                            min="{{ now()->addDay()->format('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('preferred_date') border-red-500 @enderror"
                            onchange="checkAvailableSlots()"
                            required>
                        @error('preferred_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="preferred_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Waktu Survei <span class="text-red-500">*</span>
                            <span id="loadingSlots" class="text-blue-500 text-xs ml-2 hidden">⏳ Mengecek...</span>
                        </label>
                        <select 
                            id="preferred_time" 
                            name="preferred_time"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('preferred_time') border-red-500 @enderror"
                            required>
                            <option value="{{ old('preferred_time', $surveyBooking->preferred_time) }}" selected>
                                {{ old('preferred_time', $surveyBooking->preferred_time) ? \Carbon\Carbon::parse(old('preferred_time', $surveyBooking->preferred_time))->format('H:i') . ' WIB' : 'Pilih waktu' }}
                            </option>
                        </select>
                        <p id="slotInfo" class="text-xs text-gray-500 mt-1"></p>
                        @error('preferred_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-8">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Tambahan (Opsional)
                    </label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Informasi tambahan yang perlu diketahui admin">{{ old('notes', $surveyBooking->notes) }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('survey-booking.show', $surveyBooking) }}" class="text-gray-600 hover:text-gray-800">
                        Batal
                    </a>
                    <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition duration-200">
                        💾 Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Initialize map
    const initialLat = {{ $surveyBooking->latitude ?? '-6.9175' }};
    const initialLng = {{ $surveyBooking->longitude ?? '107.6191' }};
    const map = L.map('map').setView([initialLat, initialLng], {{ $surveyBooking->latitude ? '15' : '13' }});

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    let marker = null;

    // Add existing marker if coordinates exist
    @if($surveyBooking->latitude && $surveyBooking->longitude)
    marker = L.marker([initialLat, initialLng]).addTo(map);
    @endif

    // Set marker on click
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.marker([lat, lng]).addTo(map);
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        document.getElementById('coordsText').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    });

    // Function to check available time slots
    async function checkAvailableSlots() {
        const dateInput = document.getElementById('preferred_date');
        const timeSelect = document.getElementById('preferred_time');
        const loadingIndicator = document.getElementById('loadingSlots');
        const slotInfo = document.getElementById('slotInfo');
        const selectedDate = dateInput.value;

        if (!selectedDate) {
            return;
        }

        loadingIndicator.classList.remove('hidden');
        timeSelect.disabled = true;

        try {
            const response = await fetch(`{{ route('survey-booking.available-slots') }}?date=${selectedDate}`);
            const data = await response.json();

            timeSelect.innerHTML = '<option value="">Pilih Waktu</option>';

            const timeSlotLabels = {
                '08:00': '08:00 - 10:00 WIB',
                '10:00': '10:00 - 12:00 WIB',
                '13:00': '13:00 - 15:00 WIB',
                '15:00': '15:00 - 17:00 WIB'
            };

            if (data.available_slots.length === 0) {
                timeSelect.innerHTML = '<option value="">Tidak ada slot tersedia</option>';
                slotInfo.textContent = '❌ Semua slot sudah terisi';
                slotInfo.className = 'text-xs text-red-500 mt-1';
            } else {
                data.available_slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = timeSlotLabels[slot] || slot;
                    timeSelect.appendChild(option);
                });

                slotInfo.textContent = `✅ ${data.available_slots.length} slot tersedia`;
                slotInfo.className = 'text-xs text-green-500 mt-1';
            }
        } catch (error) {
            console.error('Error fetching slots:', error);
            slotInfo.textContent = '⚠️ Gagal memuat slot';
            slotInfo.className = 'text-xs text-red-500 mt-1';
        } finally {
            loadingIndicator.classList.add('hidden');
            timeSelect.disabled = false;
        }
    }
</script>
@endsection
