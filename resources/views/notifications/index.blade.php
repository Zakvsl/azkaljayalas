@extends('layouts.app')
@include('components.navbar')
@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Notifikasi</h1>
            <p class="text-gray-600 mt-2">Kelola semua notifikasi Anda</p>
        </div>

        <!-- Stats & Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-6">
                    <div>
                        <p class="text-sm text-gray-500">Total Notifikasi</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="h-12 w-px bg-gray-300"></div>
                    <div>
                        <p class="text-sm text-gray-500">Belum Dibaca</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['unread'] }}</p>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            Tandai Semua Dibaca
                        </button>
                    </form>
                    <form action="{{ route('notifications.delete-all-read') }}" method="POST" class="inline"
                        onsubmit="return confirm('Hapus semua notifikasi yang sudah dibaca?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm font-medium">
                            Hapus Yang Dibaca
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px overflow-x-auto">
                    <a href="{{ route('notifications.index') }}" 
                        class="px-6 py-4 text-sm font-medium border-b-2 {{ !request('type') && !request('read_status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Semua ({{ $stats['total'] }})
                    </a>
                    <a href="{{ route('notifications.index', ['read_status' => 'unread']) }}" 
                        class="px-6 py-4 text-sm font-medium border-b-2 {{ request('read_status') === 'unread' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Belum Dibaca ({{ $stats['unread'] }})
                    </a>
                    <a href="{{ route('notifications.index', ['type' => 'booking']) }}" 
                        class="px-6 py-4 text-sm font-medium border-b-2 {{ request('type') === 'booking' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Booking ({{ $stats['booking'] }})
                    </a>
                    <a href="{{ route('notifications.index', ['type' => 'price_offer']) }}" 
                        class="px-6 py-4 text-sm font-medium border-b-2 {{ request('type') === 'price_offer' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Penawaran ({{ $stats['price_offer'] }})
                    </a>
                    <a href="{{ route('notifications.index', ['type' => 'payment']) }}" 
                        class="px-6 py-4 text-sm font-medium border-b-2 {{ request('type') === 'payment' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Pembayaran ({{ $stats['payment'] }})
                    </a>
                    <a href="{{ route('notifications.index', ['type' => 'order_progress']) }}" 
                        class="px-6 py-4 text-sm font-medium border-b-2 {{ request('type') === 'order_progress' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Order ({{ $stats['order_progress'] }})
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
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Notifikasi</h3>
                <p class="text-gray-500">Belum ada notifikasi untuk ditampilkan</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    <div class="p-6 hover:bg-gray-50 transition duration-150 {{ $notification->is_read ? 'opacity-75' : 'bg-blue-50' }}">
                        <div class="flex items-start space-x-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $notification->color }}">
                                    <span class="text-lg">{{ $notification->icon }}</span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $notification->title }}
                                            @if(!$notification->is_read)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Baru
                                                </span>
                                            @endif
                                        </p>
                                        <p class="mt-1 text-sm text-gray-700">{{ $notification->message }}</p>
                                        <p class="mt-2 text-xs text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                            @if($notification->read_at)
                                                · Dibaca {{ $notification->read_at->diffForHumans() }}
                                            @endif
                                        </p>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2 ml-4">
                                        @if(!$notification->is_read)
                                            <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium"
                                                    title="Tandai dibaca">
                                                    ✓ Baca
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST"
                                            onsubmit="return confirm('Hapus notifikasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="text-red-600 hover:text-red-800 text-xs font-medium"
                                                title="Hapus">
                                                × Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Action Button (if applicable) -->
                                @if($notification->survey_booking_id)
                                    <div class="mt-3">
                                        @if($notification->type === 'price_offer')
                                            <a href="{{ route('survey-booking.price-offer', $notification->survey_booking_id) }}" 
                                                class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-800 bg-green-100 px-4 py-2 rounded-lg">
                                                💰 Lihat Penawaran Harga
                                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        @else
                                            <a href="{{ route('survey-booking.show', $notification->survey_booking_id) }}" 
                                                class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                                Lihat Detail
                                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                @endif
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
