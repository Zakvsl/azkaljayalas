<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio {{ $category ? ucfirst($category) : 'Semua' }} - Azkal Jaya Las</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
        }
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
        .aspect-square {
            aspect-ratio: 1 / 1;
        }
    </style>
</head>
<body class="bg-gray-100">
    <x-navbar />

    <!-- Breadcrumb -->
    <div class="bg-white border-b mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-colors mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span>Beranda</span>
                <span>/</span>
                <span>Kategori</span>
                <span>/</span>
                <span class="text-blue-600">{{ $category ? ucfirst($category) : 'Semua' }}</span>
            </div>
        </div>
    </div>

    <!-- Category Header -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $category && $category !== 'semua' ? ucfirst($category) : 'Semua Portfolio' }}</h1>
            <p class="text-gray-600 text-lg mb-6">
                Portfolio hasil proyek {{ $category && $category !== 'semua' ? strtolower($category) : '' }} yang telah kami kerjakan dengan kualitas terbaik
            </p>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <p class="text-gray-600">Menampilkan {{ count($portfolios) }} proyek</p>
        </div>
        @if(count($portfolios) > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                @foreach($portfolios as $portfolio)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group cursor-pointer">
                    <div class="aspect-square overflow-hidden relative">
                        <img src="{{ $portfolio['image'] }}" 
                             alt="{{ $portfolio['title'] }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-2 right-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            {{ $portfolio['year'] }}
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-gray-900 mb-2 line-clamp-1">{{ $portfolio['title'] }}</h3>
                        <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ $portfolio['material'] }}</p>
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                            <span>{{ $portfolio['location'] }}</span>
                            @if(isset($portfolio['size']))
                                <span>{{ $portfolio['size'] }}</span>
                            @endif
                        </div>
                        <a href="{{ route('estimates.create') }}"
                           class="w-full text-blue-600 border border-blue-600 hover:bg-blue-600 hover:text-white py-2 px-4 rounded text-sm transition-colors text-center block">
                            Estimasi Harga
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- CTA Section -->
            <div class="mt-12 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-8 text-center text-white">
                <h2 class="text-2xl font-bold text-white mb-3">Tidak Menemukan yang Anda Cari?</h2>
                <p class="text-white/90 mb-6">
                    Kami menerima custom design sesuai kebutuhan Anda. Konsultasi gratis dengan tim ahli kami!
                </p>
                <a href="{{ route('estimates.create') }}"
                   class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    Hubungi Kami Sekarang
                </a>
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Portfolio</h3>
                <p class="text-sm text-gray-500 mb-6">Portfolio untuk kategori ini sedang dalam proses</p>
                <a href="{{ route('portfolio.index', ['category' => 'semua']) }}" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                    Lihat Semua Portfolio
                </a>
            </div>
        @endif
    </div>

    <x-footer />
</body>
</html>
