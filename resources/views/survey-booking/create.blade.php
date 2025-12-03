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
            <a href="{{ route('survey-booking.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Booking
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-4">Booking Survei Lokasi</h1>
            <p class="text-gray-600 mt-2">Isi form di bawah untuk menjadwalkan survei lokasi proyek Anda</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form action="{{ route('survey-booking.store') }}" method="POST" id="bookingForm">
                @csrf

                @if($priceEstimate)
                    <input type="hidden" name="price_estimate_id" value="{{ $priceEstimate->id }}">
                    
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="font-semibold text-blue-900 mb-2">📋 Estimasi Terkait</h3>
                        <p class="text-sm text-blue-800">
                            Estimasi harga awal: <strong>Rp {{ number_format($priceEstimate->harga_akhir, 0, ',', '.') }}</strong>
                        </p>
                        <p class="text-xs text-blue-600 mt-1">Harga final akan ditentukan setelah survei lokasi</p>
                    </div>
                @endif

                <!-- Project Type -->
                <div class="mb-6">
                    <label for="project_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Proyek <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="project_type" 
                        name="project_type" 
                        value="{{ old('project_type') }}"
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
                        required>{{ old('project_description') }}</textarea>
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
                        required>{{ old('location') }}</textarea>
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
                            value="{{ old('whatsapp_number') }}"
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
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                    <p class="text-xs text-gray-500 mt-2" id="coordsDisplay">
                        Koordinat: <span id="coordsText">Belum dipilih</span>
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
                            value="{{ old('preferred_date') }}"
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
                            <option value="">Pilih waktu</option>
                            <option value="08:00">08:00 - 10:00 WIB</option>
                            <option value="10:00">10:00 - 12:00 WIB</option>
                            <option value="13:00">13:00 - 15:00 WIB</option>
                            <option value="15:00">15:00 - 17:00 WIB</option>
                        </select>
                        <p id="slotInfo" class="text-xs text-gray-500 mt-1">Pilih tanggal untuk cek ketersediaan slot</p>
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
                        placeholder="Informasi tambahan yang perlu diketahui admin">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('survey-booking.index') }}" class="text-gray-600 hover:text-gray-800">
                        Batal
                    </a>
                    <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition duration-200">
                        Kirim Booking Survei
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h4 class="font-semibold text-yellow-900 mb-2">ℹ️ Informasi Penting</h4>
            <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
                <li>Admin akan menghubungi Anda via WhatsApp untuk konfirmasi</li>
                <li>Survei dilakukan untuk mendapatkan harga yang akurat</li>
                <li>Tidak ada biaya untuk survei lokasi</li>
                <li>Booking dapat dibatalkan jika belum dikonfirmasi admin</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Auto-fill dari estimasi jika ada
    @if($priceEstimate)
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== Survey Booking Auto-fill Debug ===');
        console.log('✅ Data dari database tersedia');
        
        const estimateData = {
            jenis_produk: '{{ $priceEstimate->jenis_produk ?? "" }}',
            jenis_material: '{{ $priceEstimate->jenis_material ?? "" }}',
            profile_size: '{{ $priceEstimate->profile_size ?? "" }}',
            ketebalan_mm: '{{ $priceEstimate->ketebalan_mm ?? "" }}',
            ukuran_m2: '{{ $priceEstimate->ukuran_m2 ?? "" }}',
            ukuran_m: '{{ $priceEstimate->ukuran_m ?? "" }}',
            jumlah_lubang: '{{ $priceEstimate->jumlah_lubang ?? "" }}',
            jumlah_unit: '{{ $priceEstimate->jumlah_unit ?? "" }}',
            finishing: '{{ $priceEstimate->finishing ?? "" }}',
            harga_akhir: '{{ $priceEstimate->harga_akhir ?? "" }}'
        };
        
        console.log('Database data:', estimateData);
        
        // Auto-fill project type
        const projectTypeSelect = document.getElementById('project_type');
        const projectType = estimateData.jenis_produk ? estimateData.jenis_produk.toLowerCase() : '';
        if (projectType && projectTypeSelect) {
            projectTypeSelect.value = projectType;
            console.log('✅ Project type set to:', projectType);
        }
        
        // Auto-fill project description
        const projectDesc = document.getElementById('project_description');
        if (projectDesc && !projectDesc.value) {
            let autoFillText = '';
            
            // Jenis Produk
            if (estimateData.jenis_produk) {
                autoFillText += 'Jenis Produk: ' + estimateData.jenis_produk + ',';
            }
            
            // Material
            if (estimateData.jenis_material) {
                const materialMap = {
                    'hollow': 'Hollow',
                    'hollow_stainless': 'Hollow Stainless',
                    'pipa_stainless': 'Pipa Stainless',
                    'besi_siku': 'Besi Siku',
                    'aluminium': 'Aluminium',
                    'stainless': 'Stainless Steel',
                    'plat': 'Plat'
                };
                const material = materialMap[estimateData.jenis_material] || estimateData.jenis_material;
                autoFillText += ' Material: ' + material + ',';
            }
            
            // Profile size
            if (estimateData.profile_size) {
                autoFillText += ' Ukuran Profile: ' + estimateData.profile_size + ',';
            }
            
            // Ketebalan
            if (estimateData.ketebalan_mm) {
                autoFillText += ' Ketebalan: ' + estimateData.ketebalan_mm + ' mm,';
            }
            
            // Ukuran/Dimensi
            if (estimateData.ukuran_m2 && estimateData.ukuran_m2 > 0) {
                autoFillText += ' Ukuran: ' + estimateData.ukuran_m2 + ' m²,';
            } else if (estimateData.ukuran_m && estimateData.ukuran_m > 0) {
                autoFillText += ' Panjang: ' + estimateData.ukuran_m + ' m,';
            } else if (estimateData.jumlah_lubang && estimateData.jumlah_lubang > 0) {
                autoFillText += ' Jumlah Lubang: ' + estimateData.jumlah_lubang + ' lubang,';
            }
            
            // Jumlah unit
            if (estimateData.jumlah_unit && estimateData.jumlah_unit > 1) {
                autoFillText += ' Jumlah Unit: ' + estimateData.jumlah_unit + ',';
            }
            
            // Finishing
            if (estimateData.finishing) {
                const finishingMap = {
                    'cat_biasa': 'Cat Biasa',
                    'cat_epoxy': 'Cat Epoxy',
                    'powder_coating': 'Powder Coating',
                    'galvanis': 'Galvanis',
                    'tanpa_cat': 'Tanpa Cat'
                };
                const finishing = finishingMap[estimateData.finishing] || estimateData.finishing;
                autoFillText += ' Finishing: ' + finishing + ',';
            }
            
            // Estimasi harga
            if (estimateData.harga_akhir) {
                const price = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(estimateData.harga_akhir);
                autoFillText += ' Estimasi Harga Awal: ' + price + ',';
            }
        
            projectDesc.value = autoFillText;
            console.log('Form berhasil di-auto-fill!');
        }
    });
    @endif

    // Initialize map (default to Bandung - lokasi Azkal Jaya Las)
    const map = L.map('map').setView([-6.9175, 107.6191], 13);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    let marker = null;

    // Set marker on click
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        // Remove old marker if exists
        if (marker) {
            map.removeLayer(marker);
        }

        // Add new marker
        marker = L.marker([lat, lng]).addTo(map);

        // Update hidden inputs
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        // Update display
        document.getElementById('coordsText').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    });

    // If old values exist, show marker
    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    
    if (oldLat && oldLng) {
        marker = L.marker([oldLat, oldLng]).addTo(map);
        map.setView([oldLat, oldLng], 15);
        document.getElementById('coordsText').textContent = `${parseFloat(oldLat).toFixed(6)}, ${parseFloat(oldLng).toFixed(6)}`;
    }

    // Try to get user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            
            // Only center map if no marker yet
            if (!marker) {
                map.setView([userLat, userLng], 15);
            }
        });
    }

    // Function to check available time slots
    async function checkAvailableSlots() {
        const dateInput = document.getElementById('preferred_date');
        const timeSelect = document.getElementById('preferred_time');
        const loadingIndicator = document.getElementById('loadingSlots');
        const slotInfo = document.getElementById('slotInfo');
        const selectedDate = dateInput.value;

        if (!selectedDate) {
            // Reset to default slots
            slotInfo.textContent = 'Pilih tanggal untuk cek ketersediaan slot';
            slotInfo.className = 'text-xs text-gray-500 mt-1';
            timeSelect.innerHTML = `
                <option value="">Pilih waktu</option>
                <option value="08:00">08:00 - 10:00 WIB</option>
                <option value="10:00">10:00 - 12:00 WIB</option>
                <option value="13:00">13:00 - 15:00 WIB</option>
                <option value="15:00">15:00 - 17:00 WIB</option>
            `;
            return;
        }

        // Show loading
        loadingIndicator.classList.remove('hidden');
        slotInfo.textContent = 'Mengecek ketersediaan...';
        slotInfo.className = 'text-xs text-blue-500 mt-1';
        timeSelect.disabled = true;

        try {
            const response = await fetch(`{{ route('survey-booking.available-slots') }}?date=${selectedDate}`);
            const data = await response.json();
            
            console.log('Available slots response:', data); // Debug

            // Clear and rebuild options
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
                slotInfo.className = 'text-xs text-green-600 mt-1';
            }
        } catch (error) {
            console.error('Error fetching slots:', error);
            slotInfo.textContent = '⚠️ Gagal memuat slot';
            slotInfo.className = 'text-xs text-red-500 mt-1';
            // Reset to default slots on error
            timeSelect.innerHTML = `
                <option value="">Pilih waktu</option>
                <option value="08:00">08:00 - 10:00 WIB</option>
                <option value="10:00">10:00 - 12:00 WIB</option>
                <option value="13:00">13:00 - 15:00 WIB</option>
                <option value="15:00">15:00 - 17:00 WIB</option>
            `;
        } finally {
            loadingIndicator.classList.add('hidden');
            timeSelect.disabled = false;
        }
    }
</script>
@endsection
