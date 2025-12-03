@extends('layouts.app')
@include('components.navbar')
@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Booking Survei Saya</h1>
                <p class="text-gray-600 mt-2">Kelola booking survei lokasi Anda</p>
            </div>
            <a href="{{ route('survey-booking.create') }}" 
                class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Booking Survei Baru
            </a>
        </div>

        @if($bookings->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Booking Survei</h3>
                <p class="text-gray-500 mb-6">Mulai dengan membuat booking survei pertama Anda</p>
                <a href="{{ route('survey-booking.create') }}" 
                    class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                    Buat Booking Sekarang
                </a>
            </div>
        @else
            <!-- Bookings List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyek</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bookings as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->project_type }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($booking->project_description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ Str::limit($booking->location, 40) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $booking->preferred_date->format('d M Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($booking->preferred_time)->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($booking->status === 'pending')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                ⏳ Menunggu Konfirmasi
                                            </span>
                                        @elseif($booking->status === 'confirmed')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                ✅ Dikonfirmasi
                                            </span>
                                        @elseif($booking->status === 'completed')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                ✔️ Selesai
                                            </span>
                                        @elseif($booking->status === 'cancelled')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                ❌ Dibatalkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('survey-booking.show', $booking) }}" 
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                            Detail
                                        </a>
                                        @if($booking->status === 'pending')
                                            <a href="{{ route('survey-booking.edit', $booking) }}" 
                                                class="text-green-600 hover:text-green-900 mr-3">
                                                Edit
                                            </a>
                                            <form action="{{ route('survey-booking.destroy', $booking) }}" 
                                                method="POST" 
                                                class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus booking ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
