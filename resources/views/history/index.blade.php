@extends('layouts.app')
@include('components.navbar')
@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    
    <div class="max-w-7xl mx-auto mt-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Riwayat Aktivitas</h1>
            <p class="text-gray-600 mt-2">Pantau semua aktivitas booking, survey, penawaran, dan pesanan Anda</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Booking</p>
                        <p class="text-2xl font-bold text-blue-600 mt-2">{{ $stats['booking'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Survey</p>
                        <p class="text-2xl font-bold text-green-600 mt-2">{{ $stats['survey'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Penawaran</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-2">{{ $stats['penawaran'] }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pesanan</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">{{ $stats['pesanan'] }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px overflow-x-auto">
                    <a href="{{ route('history.index', ['category' => 'booking']) }}" 
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm {{ $category === 'booking' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Booking Survey
                    </a>
                    <a href="{{ route('history.index', ['category' => 'survey']) }}" 
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm {{ $category === 'survey' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Hasil Survey
                    </a>
                    <a href="{{ route('history.index', ['category' => 'penawaran']) }}" 
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm {{ $category === 'penawaran' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Penawaran Harga
                    </a>
                    <a href="{{ route('history.index', ['category' => 'pesanan']) }}" 
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm {{ $category === 'pesanan' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Pesanan
                    </a>
                </nav>
            </div>

            <div class="p-6">
                @if($data->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Data</h3>
                        <p class="text-gray-500">Tidak ada {{ $category }} yang tercatat</p>
                    </div>
                @else
                    @if($category === 'booking')
                        @include('history.partials.booking-list')
                    @elseif($category === 'survey')
                        @include('history.partials.survey-list')
                    @elseif($category === 'penawaran')
                        @include('history.partials.penawaran-list')
                    @else
                        @include('history.partials.pesanan-list')
                    @endif

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $data->appends(['category' => $category])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
