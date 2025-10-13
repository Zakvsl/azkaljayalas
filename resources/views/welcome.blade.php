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
<body class="bg-gray-50">
    <!-- Navbar -->
    @include('components.navbar')

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
    <section id="portfolio" class="py-20 bg-white" x-data="{ activeFilter: 'semua' }">
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
    @include('components.footer')

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
