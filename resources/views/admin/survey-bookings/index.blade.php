@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Booking Survei</h2>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.survey-bookings.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       name="search" 
                       placeholder="Cari berdasarkan nama, lokasi, atau tipe proyek..." 
                       value="{{ request('search') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="min-w-[150px]">
                <select name="status" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Filter
            </button>
            @if(request('search') || request('status') != 'all')
                <a href="{{ route('admin.survey-bookings.index') }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Proyek</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($surveys as $survey)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $survey->preferred_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $survey->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $survey->user->phone_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ ucfirst($survey->project_type) }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($survey->project_description, 30) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ Str::limit($survey->location, 40) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.survey-bookings.update-status', $survey) }}" 
                                  method="POST" 
                                  onchange="this.submit()">
                                @csrf
                                @method('PATCH')
                                <select name="status" 
                                        class="text-sm rounded-full px-3 py-1 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500
                                        {{ $survey->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : '' }}
                                        {{ $survey->status === 'approved' ? 'bg-green-100 text-green-800 border-green-300' : '' }}
                                        {{ $survey->status === 'completed' ? 'bg-blue-100 text-blue-800 border-blue-300' : '' }}
                                        {{ $survey->status === 'cancelled' ? 'bg-red-100 text-red-800 border-red-300' : '' }}">
                                    <option value="pending" {{ $survey->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $survey->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="completed" {{ $survey->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $survey->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('admin.survey-bookings.show', $survey) }}" 
                               class="text-blue-600 hover:text-blue-900">Detail</a>
                            <form action="{{ route('admin.survey-bookings.destroy', $survey) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data booking survei
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($surveys->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $surveys->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
