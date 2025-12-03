@extends('layouts.app')
@include('components.navbar')
@section('styles')
@if($surveyBooking->latitude && $surveyBooking->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #detailMap {
        height: 300px;
        border-radius: 0.5rem;
        z-index: 1;
    }
</style>
@endif
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
            <h1 class="text-3xl font-bold text-gray-900 mt-4">Detail Booking Survei</h1>
        </div>

        <!-- Status Badge -->
        <div class="mb-6">
            @if($surveyBooking->status === 'pending')
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Menunggu Konfirmasi Admin</strong> - Kami akan menghubungi Anda segera.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($surveyBooking->status === 'confirmed')
                <div class="bg-green-50 border-l-4 border-green-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                <strong>Booking Dikonfirmasi</strong> - Survei akan dilakukan sesuai jadwal.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($surveyBooking->status === 'cancelled')
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                <strong>Booking Dibatalkan</strong>
                                @if($surveyBooking->cancel_reason)
                                    <br>Alasan: {{ $surveyBooking->cancel_reason }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Booking Details -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Informasi Booking</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500">Jenis Proyek</label>
                    <p class="text-gray-900 font-semibold">{{ $surveyBooking->project_type }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Tanggal Booking</label>
                    <p class="text-gray-900">{{ $surveyBooking->created_at ? $surveyBooking->created_at->format('d M Y, H:i') . ' WIB' : 'Tidak tersedia' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-500">Deskripsi Proyek</label>
                    <p class="text-gray-900">{{ $surveyBooking->project_description }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-500">Lokasi</label>
                    <p class="text-gray-900">{{ $surveyBooking->location }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Tanggal Survei</label>
                    <p class="text-gray-900 font-semibold">{{ $surveyBooking->preferred_date ? $surveyBooking->preferred_date->format('d M Y') : 'Tidak tersedia' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Waktu Survei</label>
                    <p class="text-gray-900 font-semibold">{{ $surveyBooking->preferred_time ? \Carbon\Carbon::parse($surveyBooking->preferred_time)->format('H:i') . ' WIB' : 'Tidak tersedia' }}</p>
                </div>

                @if($surveyBooking->notes)
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-500">Catatan</label>
                        <p class="text-gray-900">{{ $surveyBooking->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Map -->
        @if($surveyBooking->latitude && $surveyBooking->longitude)
            <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Lokasi di Peta</h2>
                <div id="detailMap"></div>
                <p class="text-sm text-gray-500 mt-2">
                    Koordinat: {{ number_format($surveyBooking->latitude, 6) }}, {{ number_format($surveyBooking->longitude, 6) }}
                </p>
            </div>
        @endif

        <!-- Order Info (if exists) -->
        @if($surveyBooking->order)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">📦 Informasi Order</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-blue-700">Nomor Order</label>
                        <p class="text-blue-900 font-semibold">{{ $surveyBooking->order->order_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-blue-700">Status</label>
                        <p class="text-blue-900">{{ $surveyBooking->order->status_label }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('orders.show', $surveyBooking->order) }}" 
                        class="text-blue-600 hover:text-blue-800 font-medium">
                        Lihat Detail Order →
                    </a>
                </div>
            </div>
        @endif

        <!-- Actions -->
        @if($surveyBooking->status === 'pending')
            <div class="flex space-x-4">
                <a href="{{ route('survey-booking.edit', $surveyBooking) }}" 
                    class="flex-1 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 text-center transition duration-200">
                    Edit Booking
                </a>
                <form action="{{ route('survey-booking.destroy', $surveyBooking) }}" 
                    method="POST" 
                    class="flex-1"
                    onsubmit="return confirm('Yakin ingin menghapus booking ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition duration-200">
                        Hapus Booking
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@if($surveyBooking->latitude && $surveyBooking->longitude)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const lat = {{ $surveyBooking->latitude }};
    const lng = {{ $surveyBooking->longitude }};
    
    const map = L.map('detailMap').setView([lat, lng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);
    
    L.marker([lat, lng]).addTo(map)
        .bindPopup('<strong>{{ $surveyBooking->project_type }}</strong><br>{{ Str::limit($surveyBooking->location, 50) }}')
        .openPopup();
</script>
@endif
@endsection
