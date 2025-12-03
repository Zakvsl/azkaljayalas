@extends('layouts.admin')

@section('content')
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-900">Notifikasi Admin</h1>
                <span class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded-full">ADMIN</span>
            </div>
            <p class="text-gray-600 mt-2">Monitor aktivitas customer dan booking terbaru dari seluruh sistem</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-sm text-gray-500">Total Aktivitas</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg shadow-md p-6">
                <p class="text-sm text-blue-600">Booking Baru (7 Hari)</p>
                <p class="text-2xl font-bold text-blue-700">{{ $stats['new_bookings'] }}</p>
            </div>
            <div class="bg-green-50 rounded-lg shadow-md p-6">
                <p class="text-sm text-green-600">Pembayaran Baru (7 Hari)</p>
                <p class="text-2xl font-bold text-green-700">{{ $stats['new_payments'] }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg shadow-md p-6">
                <p class="text-sm text-purple-600">Total Booking</p>
                <p class="text-2xl font-bold text-purple-700">{{ $stats['booking'] }}</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px overflow-x-auto">
                    <a href="{{ route('admin.notifications.index') }}" 
                        class="px-6 py-4 text-sm font-medium border-b-2 {{ !request('type') && !request('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Semua ({{ $stats['total'] }})
                    </a>
                    <a href="{{ route('admin.notifications.index', ['status' => 'new']) }}" 
                        class="px-6 py-4 text-sm font-medium border-b-2 {{ request('status') === 'new' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Terbaru (7 Hari)
                    </a>
                    <a href="{{ route('admin.notifications.index', ['type' => 'booking']) }}" 
                        class="px-6 py-4 text-sm font-medium border-b-2 {{ request('type') === 'booking' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Booking ({{ $stats['booking'] }})
                    </a>
                    <a href="{{ route('admin.notifications.index', ['type' => 'payment']) }}" 
                        class="px-6 py-4 text-sm font-medium border-b-2 {{ request('type') === 'payment' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Pembayaran ({{ $stats['payment'] }})
                    </a>
                </nav>
            </div>
        </div>

        <!-- Notifications List -->
        @if($notifications->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Aktivitas</h3>
                <p class="text-gray-500">Belum ada aktivitas customer untuk ditampilkan</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    <div class="p-6 hover:bg-gray-50 transition duration-150 border-l-4 {{ $notification->type === 'booking' ? 'border-blue-500' : ($notification->type === 'payment' ? 'border-green-500' : 'border-gray-300') }}">
                        <div class="flex items-start space-x-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $notification->color }}">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($notification->type === 'booking')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        @elseif($notification->type === 'payment')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        @endif
                                    </svg>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-sm font-semibold text-gray-900">{{ $notification->title }}</p>
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $notification->type === 'booking' ? 'bg-blue-100 text-blue-700' : ($notification->type === 'payment' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700') }}">
                                                {{ strtoupper($notification->type) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            @if($notification->surveyBooking && $notification->surveyBooking->user)
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <p class="text-xs text-gray-600 font-medium">{{ $notification->surveyBooking->user->name }}</p>
                                            </div>
                                            @endif
                                            <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <div class="flex-shrink-0 ml-4">
                                        @if($notification->type === 'booking' && $notification->survey_booking_id)
                                            <a href="{{ route('admin.survey-bookings.show', $notification->survey_booking_id) }}" 
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">
                                                Lihat Detail
                                            </a>
                                        @elseif($notification->type === 'payment' && $notification->payment_id)
                                            <a href="{{ route('admin.payments.show', $notification->payment_id) }}" 
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100">
                                                Lihat Pembayaran
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
