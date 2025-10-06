@extends('layouts.admin')

@section('content')
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
                <form action="{{ route('admin.survey-bookings.update-status', $surveyBooking) }}" 
                      method="POST" 
                      class="flex items-center justify-between">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700">Status:</label>
                        <select name="status" 
                                class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="pending" {{ $surveyBooking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $surveyBooking->status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="completed" {{ $surveyBooking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $surveyBooking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Update Status
                        </button>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $surveyBooking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $surveyBooking->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $surveyBooking->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $surveyBooking->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($surveyBooking->status) }}
                    </span>
                </form>
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
                            <p class="text-gray-900 whitespace-pre-line">{{ $surveyBooking->location }}</p>
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
@endsection
