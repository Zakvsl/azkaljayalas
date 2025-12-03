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
    
    <style>
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Poppins', sans-serif;
        }
        h2 {
            font-size: 1.875rem;
            font-weight: 700;
        }
        h3 {
            font-size: 1.125rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100">
    <!-- Navbar -->
    @include('components.navbar')

    <!-- Hero Banner Carousel -->
    <section class="bg-white pt-18">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div x-data="{ 
                currentSlide: 0,
                slides: [
                    {
                        image: 'https://images.unsplash.com/photo-1681399577093-e57cea722a9c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                        title: 'Estimasi Harga Otomatis dengan Machine Learning',
                        subtitle: 'Hitung biaya proyek konstruksi besi Anda dalam hitungan detik',
                        cta: 'Hitung Estimasi'
                    },
                    {
                        image: 'https://images.unsplash.com/photo-1601119462363-721d8e4f676e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                        title: 'Jadwalkan Survey Gratis',
                        subtitle: 'Survey lapangan gratis untuk perhitungan detail proyek Anda',
                        cta: 'Jadwalkan Survey'
                    },
                    {
                        image: 'https://images.unsplash.com/photo-1609293241092-8c4e5cf64af8?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                        title: 'Konstruksi Besi Berkualitas Tinggi',
                        subtitle: 'Pengalaman 15+ tahun melayani proyek residential & komersial',
                        cta: 'Lihat Portfolio'
                    }
                ],
                autoplay: null,
                init() {
                    this.autoplay = setInterval(() => {
                        this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                    }, 4000);
                },
                destroy() {
                    clearInterval(this.autoplay);
                },
                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                },
                prevSlide() {
                    this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
                }
            }" class="relative h-96 rounded-lg overflow-hidden group">
                <!-- Slides -->
                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="index === currentSlide" 
                         x-transition:enter="transition-opacity duration-500"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0">
                        <img :src="slide.image" :alt="slide.title" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/30 flex items-center">
                            <div class="max-w-xl px-8 md:px-16">
                                <h2 class="text-white mb-3" x-text="slide.title"></h2>
                                <p class="text-white text-lg mb-6" x-text="slide.subtitle"></p>
                                <a :href="index === 0 ? '{{ route('estimates.create') }}' : (index === 1 ? '{{ route('survey-booking.create') }}' : '{{ route('portfolio.index', ['category' => 'semua']) }}')"
                                   class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg transition-colors inline-flex items-center gap-2">
                                    <span x-text="slide.cta"></span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Navigation Arrows -->
                <button @click="prevSlide()" 
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button @click="nextSlide()" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                
                <!-- Dots -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="currentSlide = index"
                                :class="index === currentSlide ? 'bg-white w-8' : 'bg-white/50 hover:bg-white/75'"
                                class="w-2 h-2 rounded-full transition-all"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-8 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-gray-900">Kategori Populer</h2>
                <a href="{{ route('portfolio.index', ['category' => 'semua']) }}" class="text-orange-500 hover:text-orange-600 flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
                @php
                    $categories = [
                        ['image' => 'https://images.unsplash.com/photo-1762591692143-38b23bf99e6d?w=400', 'title' => 'Kanopi'],
                        ['image' => 'https://images.unsplash.com/photo-1609513811584-eb7e4b6e0ce5?w=400', 'title' => 'Pagar'],
                        ['image' => 'https://images.unsplash.com/photo-1560005360-6522fe681d14?w=400', 'title' => 'Railing'],
                        ['image' => 'https://images.unsplash.com/photo-1655936072893-921e69ae9038?w=400', 'title' => 'Tralis'],
                        ['image' => 'https://images.unsplash.com/photo-1745449562896-71ba57d1e2b3?w=400', 'title' => 'Balkon'],
                        ['image' => 'https://images.unsplash.com/photo-1681399577093-e57cea722a9c?w=400', 'title' => 'Custom']
                    ];
                @endphp
                @foreach($categories as $category)
                <a href="{{ route('portfolio.index', ['category' => strtolower($category['title'])]) }}" class="group relative overflow-hidden rounded-lg bg-white shadow-sm hover:shadow-md transition-all duration-300 aspect-square">
                    <img src="{{ $category['image'] }}" alt="{{ $category['title'] }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end">
                        <div class="p-4 w-full">
                            <h3 class="text-white text-center">{{ $category['title'] }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Estimation Form Section -->
    <section id="estimation-form" class="py-12 bg-gray-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-gray-900 mb-3">Form Estimasi Harga</h2>
                <p class="text-gray-600">
                    Masukkan data proyek Anda dan dapatkan perkiraan harga instan
                </p>
            </div>
            
            <!-- Redirect ke halaman estimasi yang sudah ada -->
            <div class="bg-white p-6 md:p-8 rounded-lg shadow-md text-center">
                <svg class="w-16 h-16 text-orange-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Siap Menghitung Estimasi?</h3>
                <p class="text-gray-600 mb-6">Klik tombol di bawah untuk mulai menghitung estimasi harga proyek Anda</p>
                <a href="{{ route('estimates.create') }}" 
                   class="inline-flex items-center gap-2 bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-8 py-3 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Mulai Hitung Estimasi
                </a>
            </div>
        </div>
    </section>
        </div>
    </section>

    <!-- Services Catalog -->
    <section id="layanan" class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-gray-900 mb-2">Semua Layanan</h2>
                    <p class="text-gray-600">Pilih layanan yang sesuai dengan kebutuhan proyek Anda</p>
                </div>
            </div>

            @php
                $services = [
                    ['image' => 'https://images.unsplash.com/photo-1762591692143-38b23bf99e6d?w=400', 'title' => 'Kanopi Minimalis', 'description' => 'Kanopi besi modern dengan berbagai pilihan material dan finishing'],
                    ['image' => 'https://images.unsplash.com/photo-1609513811584-eb7e4b6e0ce5?w=400', 'title' => 'Pagar Besi Premium', 'description' => 'Pagar besi kokoh dan estetis dengan desain custom'],
                    ['image' => 'https://images.unsplash.com/photo-1560005360-6522fe681d14?w=400', 'title' => 'Railing Tangga & Balkon', 'description' => 'Railing dengan standar keamanan tinggi berbagai model'],
                    ['image' => 'https://images.unsplash.com/photo-1655936072893-921e69ae9038?w=400', 'title' => 'Konstruksi Besi', 'description' => 'Konstruksi rangka besi untuk bangunan dan gudang'],
                    ['image' => 'https://images.unsplash.com/photo-1745449562896-71ba57d1e2b3?w=400', 'title' => 'Fabrikasi Custom', 'description' => 'Layanan fabrikasi custom untuk industri dan komersial'],
                    ['image' => 'https://images.unsplash.com/photo-1681399577093-e57cea722a9c?w=400', 'title' => 'Perbaikan & Modifikasi', 'description' => 'Layanan perbaikan dan modifikasi konstruksi besi existing'],
                    ['image' => 'https://images.unsplash.com/photo-1601119462363-721d8e4f676e?w=400', 'title' => 'Tralis Jendela', 'description' => 'Tralis jendela kokoh untuk keamanan rumah Anda'],
                    ['image' => 'https://images.unsplash.com/photo-1609293241092-8c4e5cf64af8?w=400', 'title' => 'Pergola & Gazebo', 'description' => 'Pergola dan gazebo besi untuk taman dan outdoor area']
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
                @foreach($services as $service)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="relative overflow-hidden" style="padding-bottom: 100%;">
                        <img src="{{ $service['image'] }}" alt="{{ $service['title'] }}" 
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-3 sm:p-4 flex flex-col flex-grow">
                        <h3 class="text-gray-900 mb-1.5 sm:mb-2 text-sm sm:text-base font-semibold line-clamp-1">{{ $service['title'] }}</h3>
                        <p class="text-gray-600 text-xs sm:text-sm mb-3 sm:mb-4 flex-grow line-clamp-2">{{ $service['description'] }}</p>
                        <div class="space-y-2">
                            <a href="{{ route('estimates.create') }}"
                               class="w-full bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-50 py-2 px-3 sm:px-4 rounded text-xs sm:text-sm font-medium transition-colors duration-200 text-center block">
                                Estimasi Harga
                            </a>
                            @auth
                                <a href="{{ route('survey-booking.create') }}"
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 sm:px-4 rounded text-xs sm:text-sm font-medium transition-colors duration-200 text-center block">
                                    Jadwalkan Survey
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 sm:px-4 rounded text-xs sm:text-sm font-medium transition-colors duration-200 text-center block">
                                    Jadwalkan Survey
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Survey Section -->
    <section class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-blue-900 to-blue-800 rounded-lg p-8 md:p-12 text-center text-white">
                <h2 class="text-white mb-4">Sudah Punya Gambaran Proyek Anda?</h2>
                <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
                    Jadwalkan survey lapangan gratis untuk perhitungan detail dan konsultasi langsung dengan tim ahli kami
                </p>
                @auth
                    <a href="{{ route('survey-booking.create') }}" 
                       class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Jadwalkan Survey Lapangan
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Login untuk Jadwalkan Survey
                    </a>
                @endauth
            </div>
        </div>
    </section>
    <!-- Footer -->
 @include('components.footer')
</body>
</html>
    </script>
</body>
</html>
