<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Azkal Jaya Las - Bengkel Las Profesional</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    
    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }
        .swiper-pagination-bullet-active {
            background-color: #1d4ed8 !important;
        }
        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        .animate-spin-slow {
            animation: spin-slow 15s linear infinite;
        }
        .hero-glossy-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 45% 50%, rgba(246, 224, 94, 0.4) 0%, transparent 50%),
                        radial-gradient(circle at 55% 50%, rgba(49, 130, 206, 0.5) 0%, transparent 50%);
            filter: blur(120px);
            z-index: 0;
        }
        .hero-glossy-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 40%);
            z-index: 0;
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ activeFilter: 'semua', profileOpen: false }">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm fixed w-full z-20" x-data="{ navOpen: false }">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="/" class="text-xl font-bold text-blue-700">AZKAL JAYA LAS</a>

                <!-- Centered Desktop Links -->
                <div class="hidden md:flex space-x-8">
                    <a href="/" class="text-gray-600 hover:text-blue-700">Home</a>
                    <a href="#layanan" class="text-gray-600 hover:text-blue-700">Layanan</a>
                    <a href="#tentang" class="text-gray-600 hover:text-blue-700">Tentang</a>
                    <a href="https://wa.me/6285292674783" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-blue-700">Kontak</a>
                </div>

                <!-- Right-side Auth Links -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-blue-700">Dashboard</a>
                        @endif
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-600 hover:text-blue-700 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50"
                                 style="display: none;">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Profile
                                    </div>
                                </a>
                                <a href="{{ route('password.request') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                        </svg>
                                        Reset Password
                                    </div>
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Log Out
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-700">Log in</a>
                        <a href="{{ route('register') }}" class="bg-blue-700 text-white px-6 py-2 rounded-lg hover:bg-blue-800">Sign Up</a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="md:hidden">
                    <button @click="navOpen = !navOpen" class="text-gray-600 hover:text-blue-700 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path :class="{'hidden': navOpen, 'inline-flex': !navOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                             <path :class="{'hidden': !navOpen, 'inline-flex': navOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div :class="{'block': navOpen, 'hidden': !navOpen}" class="md:hidden bg-white border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="/" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Home</a>
                <a href="#layanan" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Layanan</a>
                <a href="#tentang" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Tentang</a>
                <a href="#kontak" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Kontak</a>
            </div>
            <!-- Mobile Auth Links -->
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="px-5 space-y-3">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Dashboard</a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Profile</a>
                    <a href="{{ route('password.request') }}" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Reset Password</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left text-red-600 hover:bg-gray-100 px-3 py-2 rounded-md">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block bg-blue-700 text-white text-center px-6 py-2 rounded-lg hover:bg-blue-800">Log in</a>
                    <a href="{{ route('register') }}" class="block bg-gray-200 text-gray-700 text-center px-6 py-2 rounded-lg hover:bg-gray-300 mt-2">Sign Up</a>
                @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative hero-glossy-bg bg-blue-50 pt-32 pb-24 overflow-hidden">
        <div class="container mx-auto px-6 z-10 relative">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                        Bikin Konstruksi Besi Jadi <span class="text-blue-700">Mudah & Terjangkau</span>
                    </h1>
                    <p class="text-gray-600 text-lg mb-10">
                        Dapatkan estimasi harga akurat langsung dari sistem kami. Cukup masukkan ukuran & kebutuhan Anda, kami urus sisanya.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center lg:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                        @auth
                            <a href="{{ route('survey.create') }}" class="bg-white text-blue-700 px-8 py-3 rounded-full font-semibold shadow-md hover:bg-gray-100 transition-all transform hover:scale-105">
                                Booking Jadwal Survei
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="bg-white text-blue-700 px-8 py-3 rounded-full font-semibold shadow-md hover:bg-gray-100 transition-all transform hover:scale-105">
                                Booking Jadwal Survei
                            </a>
                        @endauth
                        <a href="{{ route('estimates.create') }}" class="bg-blue-700 text-white px-8 py-3 rounded-full font-semibold shadow-md hover:bg-blue-800 transition-all transform hover:scale-105 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 12.293a1 1 0 001.414 1.414l2.5-2.5A1 1 0 0011 10.5V7z"></path></svg>
                            Estimasi Harga
                        </a>
                    </div>
                </div>
                <div class="flex justify-center items-center relative h-80 lg:h-96 mt-12 lg:mt-0">
                    <!-- Decorative Shapes -->
                     <div class="absolute top-8 left-10 w-12 h-12 border-4 border-blue-500 rounded-md animate-spin-slow"></div>
                    <div class="absolute top-10 right-2 w-20 h-20 border-4 border-yellow-400 rounded-lg animate-spin-slow"></div>
                    <div class="absolute bottom-8 left-10 w-12 h-12 border-4 border-blue-300 rounded-md animate-spin-slow" style="animation-direction: reverse;"></div>
                    <div class="absolute bottom-2 right-16 w-8 h-8 border-2 border-gray-800 rounded-full animate-spin-slow"></div>

                    <!-- Images -->
                     <img src="{{ asset('build/assets/img/img2.png') }}" alt="Welding sparks" class="absolute w-5/12 lg:w-4/12 h-auto rounded-xl shadow-lg transform rotate-6 hover:rotate-0 transition-transform duration-300" style="right: 15%; bottom: 20%;">
                    <img src="{{ asset('build/assets/img/img1.png') }}" alt="Welder working on metal" class="absolute w-6/12 lg:w-5/12 h-auto rounded-xl shadow-2xl transform -rotate-6 hover:rotate-0 transition-transform duration-300" style="left: 15%; top: 20%;">
                </div>
            </div>
        </div>
    </section>

    <!-- Info Card Section -->
    <section class="relative z-10">
        <div class="container mx-auto px-6">
            <div class="bg-white rounded-xl shadow-2xl p-6 md:p-8 -mt-16">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <p class="text-gray-500 text-sm">Sejak 2019 Melayani</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-blue-700">100+</p>
                        <p class="text-gray-500">Orang</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-blue-700">56</p>
                        <p class="text-gray-500">Kab/Kota</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-blue-700">3</p>
                        <p class="text-gray-500">Provinsi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kenapa Harus Section -->
    <section id="tentang" class="py-20 bg-white pt-28">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-4">Kenapa Harus <span class="text-blue-700">Azkal Jaya Las</span>?</h2>
            <p class="text-center text-gray-600 mb-12">Lebih dari Sekedar Bengkel Las. Kami Bangun dengan Kualitas, Kami Rawat dengan Kepercayaan.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-center mb-2">Estimasi Transparan</h3>
                    <p class="text-gray-600 text-center">Harga dan layanan transparan sejak awal.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-center mb-2">Material Berkualitas</h3>
                    <p class="text-gray-600 text-center">Bahan baku pilihan berkualitas tinggi.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-center mb-2">Booking Jadwal</h3>
                    <p class="text-gray-600 text-center">Sistem booking online mudah dan cepat.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-center mb-2">Tim Profesional</h3>
                    <p class="text-gray-600 text-center">Pengerjaan oleh tim ahli berpengalaman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Section -->
    <section id="layanan" class="py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-4">Layanan Profesional Kami</h2>
            <p class="text-center text-gray-600 mb-12">Solusi Lengkap untuk Kebutuhan Konstruksi Besi Anda</p>

            <div class="swiper serviceSwiper">
                <div class="swiper-wrapper">
                    <!-- Service 1 -->
                    <div class="swiper-slide">
                        <div class="bg-white rounded-xl overflow-hidden shadow-lg">
                            <img src="/img/kanopi.jpg" alt="Kanopi Modern" class="w-full h-48 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">Kanopi Modern</h3>
                                <p class="text-gray-600 mb-4">Desain kanopi modern dengan material berkualitas tinggi.</p>
                                <a href="#" class="text-blue-700 font-medium hover:text-blue-800">Selengkapnya →</a>
                            </div>
                        </div>
                    </div>

                    <!-- Service 2 -->
                    <div class="swiper-slide">
                        <div class="bg-white rounded-xl overflow-hidden shadow-lg">
                            <img src="/img/pagar.jpg" alt="Pagar Minimalis" class="w-full h-48 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">Pagar Minimalis</h3>
                                <p class="text-gray-600 mb-4">Pagar besi dengan desain minimalis dan elegan.</p>
                                <a href="#" class="text-blue-700 font-medium hover:text-blue-800">Selengkapnya →</a>
                            </div>
                        </div>
                    </div>

                    <!-- Service 3 -->
                    <div class="swiper-slide">
                        <div class="bg-white rounded-xl overflow-hidden shadow-lg">
                            <img src="/img/railing.jpg" alt="Railing Tangga" class="w-full h-48 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">Railing Tangga</h3>
                                <p class="text-gray-600 mb-4">Railing tangga custom dengan desain modern.</p>
                                <a href="#" class="text-blue-700 font-medium hover:text-blue-800">Selengkapnya →</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-4">Proyek Yang Telah Kami Kerjakan</h2>
            <p class="text-center text-gray-600 mb-8">Lihat hasil karya kami yang telah memuaskan pelanggan kami di berbagai lokasi.</p>

            <!-- Portfolio Filter -->
            <div class="flex justify-center space-x-4 mb-8">
                <button @click="activeFilter = 'semua'" 
                        :class="{ 'bg-blue-700 text-white': activeFilter === 'semua', 'bg-gray-100 text-gray-600 hover:bg-gray-200': activeFilter !== 'semua' }"
                        class="px-6 py-2 rounded-full font-medium transition-colors">
                    Semua
                </button>
                <button @click="activeFilter = 'pagar'"
                        :class="{ 'bg-blue-700 text-white': activeFilter === 'pagar', 'bg-gray-100 text-gray-600 hover:bg-gray-200': activeFilter !== 'pagar' }"
                        class="px-6 py-2 rounded-full font-medium transition-colors">
                    Pagar
                </button>
                <button @click="activeFilter = 'kanopi'"
                        :class="{ 'bg-blue-700 text-white': activeFilter === 'kanopi', 'bg-gray-100 text-gray-600 hover:bg-gray-200': activeFilter !== 'kanopi' }"
                        class="px-6 py-2 rounded-full font-medium transition-colors">
                    Kanopi
                </button>
                <button @click="activeFilter = 'railing'"
                        :class="{ 'bg-blue-700 text-white': activeFilter === 'railing', 'bg-gray-100 text-gray-600 hover:bg-gray-200': activeFilter !== 'railing' }"
                        class="px-6 py-2 rounded-full font-medium transition-colors">
                    Railing
                </button>
            </div>

            <!-- Portfolio Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Portfolio items will be added dynamically -->
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-blue-700">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Siap Memulai Proyek Anda?</h2>
            <p class="text-blue-100 mb-8">Diskusikan ide Anda dengan tim ahli kami atau langsung jadwalkan survei ke lokasi Anda sekarang juga!</p>
            @auth
            <a href="{{ route('survey.create') }}" 
               class="inline-block bg-white text-blue-700 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                JADWALKAN SURVEI SEKARANG
            </a>
            @else
            <a href="{{ route('login') }}" 
               class="inline-block bg-white text-blue-700 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                JADWALKAN SURVEI SEKARANG
            </a>
            @endauth
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Azkal Jaya Las</h3>
                    <p class="mb-4">Bengkel las modern yang berpengalaman dan berkualitas tinggi.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"></path>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"></path>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Navigasi</h3>
                    <ul class="space-y-2">
                        <li><a href="#home" class="hover:text-white">Home</a></li>
                        <li><a href="#layanan" class="hover:text-white">Layanan</a></li>
                        <li><a href="#portfolio" class="hover:text-white">Portfolio</a></li>
                        <li><a href="#tentang" class="hover:text-white">Tentang</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Hubungi Kami</h3>
                    <p class="flex items-start space-x-3 mb-4">
                        <svg class="w-6 h-6 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Jl. Soekarno Hatta, Malang, Jawa Timur</span>
                    </p>
                    <p class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>+62 852-9267-4783</span>
                    </p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                <p>&copy; 2025 Azkal Jaya Las. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper('.serviceSwiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                },
            });
        });
    </script>
</body>
</html>
