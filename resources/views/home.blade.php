@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="mb-4">Bikin Konstruksi Besi Jadi <br>Mudah & Terjangkau</h1>
                        <p class="mb-4">Dapatkan estimasi harga akurat bangunan besi dalam hitungan detik. Cukup masukkan ukuran & detail, dapatkan estimasi harga tanpa biaya.</p>
                        <div class="d-flex gap-3">
                            <a href="#prediction" class="btn btn-primary">Prediksi Harga</a>
                            <a href="#contact" class="btn btn-outline-primary">Hubungi Kami</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Konstruksi Besi" class="img-fluid">
                        <div class="shape-1"></div>
                        <div class="shape-2"></div>
                        <div class="shape-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Prediction Form -->
    <section class="prediction-form-container">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="prediction-form">
                        <h3>Hitung Estimasi Harga Proyek Anda!</h3>
                        <form action="{{ route('prediction.calculate') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="product" class="form-label">Pilih Jenis Konstruksi</label>
                                    <select class="form-select" id="product" name="product_id" required>
                                        <option value="" selected disabled>Pilih Jenis</option>
                                        <option value="1">Pagar</option>
                                        <option value="2">Kanopi</option>
                                        <option value="3">Teralis</option>
                                        <option value="4">Pintu</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="material" class="form-label">Ukuran (m²)</label>
                                    <input type="number" class="form-control" id="ukuran" name="ukuran" placeholder="Masukkan ukuran" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="finishing" class="form-label">Kerumitan Konstruksi</label>
                                    <select class="form-select" id="complexity" name="kerumitan_id" required>
                                        <option value="" selected disabled>Pilih Tingkat</option>
                                        <option value="1">Sederhana</option>
                                        <option value="2">Menengah</option>
                                        <option value="3">Kompleks</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="material" class="form-label">Ketebalan Material</label>
                                    <select class="form-select" id="thickness" name="ketebalan_id" required>
                                        <option value="" selected disabled>Pilih Ketebalan</option>
                                        <option value="1">Tipis</option>
                                        <option value="2">Sedang</option>
                                        <option value="3">Tebal</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="finishing" class="form-label">Jenis Finishing</label>
                                    <select class="form-select" id="finishing" name="finishing_id" required>
                                        <option value="" selected disabled>Pilih Finishing</option>
                                        <option value="1">Cat Dasar</option>
                                        <option value="2">Cat Premium</option>
                                        <option value="3">Galvanis</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Hitung Estimasi</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section id="about" class="why-choose-us-section">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Kenapa Harus <span class="text-primary">Azkal Jaya Las</span>?</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="service-card text-center">
                        <div class="icon-box">
                            <i class="fas fa-ruler-combined"></i>
                        </div>
                        <h4>Estimasi Akurat</h4>
                        <p>Harga yang kami berikan sesuai dengan spesifikasi dan kebutuhan anda.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="service-card text-center">
                        <div class="icon-box">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h4>Kualitas Premium</h4>
                        <p>Material berkualitas tinggi dengan pengerjaan yang rapi dan teliti.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="service-card text-center">
                        <div class="icon-box">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h4>Booking Online</h4>
                        <p>Jadwalkan survei lokasi dengan mudah melalui sistem booking online.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="service-card text-center">
                        <div class="icon-box">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h4>Tim yang Handal</h4>
                        <p>Dikerjakan oleh tim profesional dengan pengalaman bertahun-tahun.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Layanan Profesional Kami</h2>
            </div>
            <div class="swiper-container services-slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="service-slide">
                            <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Kanopi Modern" class="img-fluid">
                            <div class="service-info">
                                <h3>Kanopi Modern</h3>
                                <a href="#" class="btn btn-primary rounded-pill">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="service-slide">
                            <img src="https://images.unsplash.com/photo-1555505019-8c3f1c4aba5f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Pagar Minimalis" class="img-fluid">
                            <div class="service-info">
                                <h3>Pagar Minimalis</h3>
                                <a href="#" class="btn btn-primary rounded-pill">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="service-slide">
                            <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Teralis Artistik" class="img-fluid">
                            <div class="service-info">
                                <h3>Teralis Artistik</h3>
                                <a href="#" class="btn btn-primary rounded-pill">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="service-slide">
                            <img src="https://images.unsplash.com/photo-1600566752355-35792bedcfea?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Pintu Besi" class="img-fluid">
                            <div class="service-info">
                                <h3>Pintu Besi</h3>
                                <a href="#" class="btn btn-primary rounded-pill">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="projects-section">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Proyek Yang Telah Kami Kerjakan</h2>
                <p class="section-subtitle">Lihat hasil karya konstruksi besi berkualitas yang telah kami kerjakan untuk berbagai klien</p>
            </div>
            <div class="project-filter mb-4 text-center">
                <button class="btn btn-primary active">Semua</button>
                <button class="btn btn-outline-primary">Pagar</button>
                <button class="btn btn-outline-primary">Kanopi</button>
                <button class="btn btn-outline-primary">Teralis</button>
                <button class="btn btn-outline-primary">Pintu</button>
            </div>
            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="project-card">
                        <div class="project-img">
                            <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Proyek Pagar 1" class="img-fluid">
                        </div>
                        <div class="project-info">
                            <h5>Proyek Pagar 1</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="project-card">
                        <div class="project-img">
                            <img src="https://images.unsplash.com/photo-1555505019-8c3f1c4aba5f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Proyek Kanopi 1" class="img-fluid">
                        </div>
                        <div class="project-info">
                            <h5>Proyek Kanopi 1</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="project-card">
                        <div class="project-img">
                            <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Proyek Teralis 1" class="img-fluid">
                        </div>
                        <div class="project-info">
                            <h5>Proyek Teralis 1</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="project-card">
                        <div class="project-img">
                            <img src="https://images.unsplash.com/photo-1600566752355-35792bedcfea?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Proyek Pintu 1" class="img-fluid">
                        </div>
                        <div class="project-info">
                            <h5>Proyek Pintu 1</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="project-card">
                        <div class="project-img">
                            <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Proyek Pagar 2" class="img-fluid">
                        </div>
                        <div class="project-info">
                            <h5>Proyek Pagar 2</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="project-card">
                        <div class="project-img">
                            <img src="https://images.unsplash.com/photo-1555505019-8c3f1c4aba5f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Proyek Kanopi 2" class="img-fluid">
                        </div>
                        <div class="project-info">
                            <h5>Proyek Kanopi 2</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="project-card">
                        <div class="project-img">
                            <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Proyek Teralis 2" class="img-fluid">
                        </div>
                        <div class="project-info">
                            <h5>Proyek Teralis 2</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="project-card">
                        <div class="project-img">
                            <img src="https://images.unsplash.com/photo-1600566752355-35792bedcfea?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Proyek Pintu 2" class="img-fluid">
                        </div>
                        <div class="project-info">
                            <h5>Proyek Pintu 2</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content text-center">
                <h2>Siap Memulai Proyek Anda?</h2>
                <p>Dapatkan hasil terbaik dengan konsultasi gratis dari tim profesional kami</p>
                <a href="#contact" class="btn btn-primary btn-lg">JADWALKAN SURVEI GRATIS</a>
            </div>
        </div>
    </section>

    <!-- Prediction Section -->
    <section id="prediction" class="prediction-section">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Prediksi Harga Konstruksi Besi</h2>
                <p class="section-subtitle">Dapatkan estimasi harga akurat untuk kebutuhan konstruksi besi Anda dalam hitungan detik</p>
            </div>
            <div class="row">
                <div class="col-lg-5">
                    <div class="prediction-info">
                        <h3>Mengapa Menggunakan Kalkulator Harga Kami?</h3>
                        <div class="prediction-feature">
                            <div class="feature-icon">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div class="feature-text">
                                <h5>Estimasi Akurat</h5>
                                <p>Dapatkan perkiraan biaya yang akurat berdasarkan spesifikasi proyek Anda</p>
                            </div>
                        </div>
                        <div class="prediction-feature">
                            <div class="feature-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="feature-text">
                                <h5>Hasil Instan</h5>
                                <p>Hanya dalam hitungan detik, Anda akan mendapatkan estimasi harga</p>
                            </div>
                        </div>
                        <div class="prediction-feature">
                            <div class="feature-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="feature-text">
                                <h5>Tanpa Biaya</h5>
                                <p>Layanan prediksi harga kami 100% gratis tanpa biaya tersembunyi</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="prediction-form-box">
                        <h4>Masukkan Detail Konstruksi</h4>
                        <form action="{{ route('prediction.calculate') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="product" class="form-label">Jenis Konstruksi</label>
                                    <select class="form-select" id="product" name="product_id" required>
                                        <option value="" selected disabled>Pilih Jenis</option>
                                        <option value="1">Pagar Besi</option>
                                        <option value="2">Kanopi</option>
                                        <option value="3">Teralis</option>
                                        <option value="4">Pintu Besi</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ukuran" class="form-label">Ukuran (m²)</label>
                                    <input type="number" class="form-control" id="ukuran" name="ukuran" placeholder="Masukkan ukuran" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ketebalan" class="form-label">Ketebalan Material</label>
                                    <select class="form-select" id="ketebalan" name="ketebalan_id" required>
                                        <option value="" selected disabled>Pilih Ketebalan</option>
                                        <option value="1">Tipis (1-2mm)</option>
                                        <option value="2">Sedang (3-4mm)</option>
                                        <option value="3">Tebal (5mm+)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="kerumitan" class="form-label">Tingkat Kerumitan</label>
                                    <select class="form-select" id="kerumitan" name="kerumitan_id" required>
                                        <option value="" selected disabled>Pilih Tingkat</option>
                                        <option value="1">Sederhana</option>
                                        <option value="2">Menengah</option>
                                        <option value="3">Kompleks</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="finishing" class="form-label">Jenis Finishing</label>
                                <select class="form-select" id="finishing" name="finishing_id" required>
                                    <option value="" selected disabled>Pilih Finishing</option>
                                    <option value="1">Cat Dasar</option>
                                    <option value="2">Cat Premium</option>
                                    <option value="3">Galvanis</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">HITUNG ESTIMASI HARGA</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-info">
                        <h2 class="section-title">Azkal Jaya Las</h2>
                        <p class="mb-4">Spesialis konstruksi besi berkualitas dengan pengalaman lebih dari 10 tahun. Kami menyediakan solusi terbaik untuk kebutuhan konstruksi besi Anda.</p>
                        
                        <div class="contact-details">
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <h5>Alamat</h5>
                                    <p>Jl. Raya Bengkel No. 123, Surabaya, Indonesia</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <h5>Email</h5>
                                    <p>info@azkaljayalas.com</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone-alt"></i>
                                <div>
                                    <h5>Telepon</h5>
                                    <p>+62 812 3456 7890</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="social-media mt-4">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="booking-form">
                        <h3>Jadwalkan Survei Gratis</h3>
                        <form action="{{ route('survey-booking.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat Lokasi Survei</label>
                                <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date" class="form-label">Tanggal Survei</label>
                                    <input type="date" class="form-control" id="date" name="date" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="time" class="form-label">Waktu Survei</label>
                                    <select class="form-select" id="time" name="time" required>
                                        <option value="" selected disabled>Pilih Waktu</option>
                                        <option value="09:00">09:00</option>
                                        <option value="10:00">10:00</option>
                                        <option value="11:00">11:00</option>
                                        <option value="13:00">13:00</option>
                                        <option value="14:00">14:00</option>
                                        <option value="15:00">15:00</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Catatan Tambahan (Opsional)</label>
                                <textarea class="form-control" id="message" name="message" rows="2"></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">JADWALKAN SEKARANG</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    
    <!-- Initialize Swiper -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Services Slider
            var servicesSwiper = new Swiper('.services-slider', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 1,
                    },
                    768: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                }
            });
            
            // Project Filter
            const filterButtons = document.querySelectorAll('.project-filter button');
            const projectCards = document.querySelectorAll('.project-card');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    const filter = this.textContent.toLowerCase();
                    
                    projectCards.forEach(card => {
                        const category = card.querySelector('.project-info h5').textContent.toLowerCase();
                        
                        if (filter === 'semua' || category.includes(filter.replace('pagar', 'pagar').replace('kanopi', 'kanopi').replace('teralis', 'teralis').replace('pintu', 'pintu'))) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
            
            // Prediction Form Integration with Booking Form
            const predictionForm = document.querySelector('form[action="{{ route(\'prediction.calculate\') }}"]');
            if (predictionForm) {
                predictionForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Simulate prediction calculation
                    setTimeout(function() {
                        // Show prediction result
                        const result = Math.floor(Math.random() * 5000000) + 1000000;
                        const formattedResult = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(result);
                        
                        // Create result element
                        const resultElement = document.createElement('div');
                        resultElement.className = 'alert alert-success mt-3';
                        resultElement.innerHTML = `
                            <h5>Estimasi Harga:</h5>
                            <h3 class="mb-3">${formattedResult}</h3>
                            <p>Ingin mendapatkan penawaran yang lebih akurat?</p>
                            <a href="#contact" class="btn btn-primary">Jadwalkan Survei Gratis</a>
                        `;
                        
                        // Find or create container for result
                        let resultContainer = document.querySelector('.prediction-result');
                        if (!resultContainer) {
                            resultContainer = document.createElement('div');
                            resultContainer.className = 'prediction-result';
                            predictionForm.after(resultContainer);
                        } else {
                            resultContainer.innerHTML = '';
                        }
                        
                        resultContainer.appendChild(resultElement);
                        
                        // Scroll to result
                        resultContainer.scrollIntoView({ behavior: 'smooth' });
                    }, 1000);
                });
            }
        });
    </script>
@endsection