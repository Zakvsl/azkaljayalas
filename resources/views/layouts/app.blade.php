<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Azkal Jaya Las</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d47a1;
            --secondary-color: #1565c0;
            --accent-color: #ffb300;
            --text-color: #333;
            --light-bg: #f8f9fa;
            --dark-bg: #0a2351;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            color: var(--text-color);
        }
        
        .section {
            padding: 80px 0;
        }
        
        /* Navbar Styles */
        .navbar {
            transition: all 0.3s ease;
            padding: 15px 0;
        }
        
        .navbar-scrolled {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
        }
        
        .navbar-brand img {
            height: 40px;
        }
        
        .navbar-light .navbar-nav .nav-link {
            color: var(--text-color);
            font-weight: 500;
            padding: 0 15px;
        }
        
        .navbar-light .navbar-nav .nav-link:hover {
            color: var(--primary-color);
        }
        
        .btn-login {
            background-color: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 5px;
            padding: 8px 20px;
            margin-right: 10px;
        }
        
        .btn-signup {
            background-color: var(--primary-color);
            border: none;
            color: white;
            border-radius: 5px;
            padding: 8px 20px;
        }
        
        /* Hero Section */
        .hero-section {
            background-size: cover;
            background-position: center;
            position: relative;
            padding: 150px 0 100px;
            background-color: var(--light-bg);
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-content h1 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .hero-content p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            max-width: 600px;
        }
        
        .hero-image {
            position: relative;
        }
        
        .hero-image img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .shape-1, .shape-2, .shape-3 {
            position: absolute;
            z-index: -1;
        }
        
        .shape-1 {
            top: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            background-color: var(--accent-color);
            border-radius: 10px;
        }
        
        .shape-2 {
            bottom: -15px;
            left: -15px;
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            border-radius: 50%;
        }
        
        .shape-3 {
            top: 50%;
            right: -30px;
            width: 40px;
            height: 40px;
            background-color: var(--secondary-color);
            transform: rotate(45deg);
        }
        
        /* Prediction Form */
        .prediction-form-container {
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }
        
        .prediction-form {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        .prediction-form h3 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--text-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        /* About Section */
        .about-section {
            background-color: var(--light-bg);
        }
        
        .section-title {
            margin-bottom: 50px;
            position: relative;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
        }
        
        .text-center .section-title::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        /* Services Section */
        .service-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .service-icon {
            width: 70px;
            height: 70px;
            background-color: var(--light-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .service-icon i {
            font-size: 30px;
            color: var(--primary-color);
        }
        
        .service-card h4 {
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        /* Projects Section */
        .project-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .project-tab {
            padding: 10px 20px;
            margin: 0 5px;
            background-color: var(--light-bg);
            border: none;
            border-radius: 5px;
            font-weight: 500;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .project-tab.active, .project-tab:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .project-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            background-color: white;
        }
        
        .project-img {
            height: 200px;
            overflow: hidden;
        }
        
        .project-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .project-card:hover .project-img img {
            transform: scale(1.1);
        }
        
        .project-info {
            padding: 20px;
        }
        
        .project-info h5 {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--primary-color);
        }
        
        /* CTA Section */
        .cta-section {
            background-color: var(--dark-bg);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .cta-section h2 {
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .cta-section p {
            margin-bottom: 30px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-cta {
            background-color: var(--accent-color);
            color: var(--dark-bg);
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 5px;
            border: none;
        }
        
        .btn-cta:hover {
            background-color: #ffa000;
            color: var(--dark-bg);
        }
        
        /* Footer */
        footer {
            background-color: var(--dark-bg);
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-logo {
            margin-bottom: 20px;
        }
        
        .footer-logo img {
            height: 40px;
        }
        
        .footer-text {
            margin-bottom: 20px;
            max-width: 300px;
        }
        
        .footer-title {
            font-weight: 600;
            margin-bottom: 25px;
            font-size: 1.2rem;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .footer-contact {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }
        
        .footer-contact i {
            margin-right: 15px;
            color: var(--accent-color);
        }
        
        .social-icons {
            margin-top: 20px;
        }
        
        .social-icons a {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            background-color: var(--accent-color);
            color: var(--dark-bg);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Swiper Styles */
        .swiper {
            width: 100%;
            padding-bottom: 50px;
        }
        
        .swiper-pagination-bullet-active {
            background-color: var(--primary-color);
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .prediction-form-container {
                margin-top: 50px;
            }
            
            .hero-section {
                padding: 120px 0 80px;
            }
            
            .hero-image {
                margin-top: 50px;
            }
        }
        
        @media (max-width: 767px) {
            .section {
                padding: 60px 0;
            }
            
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .service-card, .project-card {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <span class="fw-bold text-primary">AZKAL JAYA LAS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#layanan">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="#login" class="btn-login me-2">Log In</a>
                    <a href="#signup" class="btn-signup">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="footer-logo">
                        <span class="fw-bold text-white">AZKAL JAYA LAS</span>
                    </div>
                    <p class="footer-text">Perusahaan konstruksi besi yang menyediakan layanan berkualitas tinggi dengan harga terjangkau untuk kebutuhan konstruksi Anda.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5 class="footer-title">Navigasi</h5>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#layanan">Layanan</a></li>
                        <li><a href="#tentang">Tentang</a></li>
                        <li><a href="#projects">Proyek</a></li>
                        <li><a href="#contact">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="footer-title">Layanan</h5>
                    <ul class="footer-links">
                        <li><a href="#">Konstruksi Besi</a></li>
                        <li><a href="#">Pembuatan Pagar</a></li>
                        <li><a href="#">Pembuatan Kanopi</a></li>
                        <li><a href="#">Pembuatan Teralis</a></li>
                        <li><a href="#">Pembuatan Pintu</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title">Hubungi Kami</h5>
                    <div class="footer-contact">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <p>Jl. Raya Utama No. 123<br>Kota, Indonesia 12345</p>
                        </div>
                    </div>
                    <div class="footer-contact">
                        <i class="fas fa-phone"></i>
                        <div>
                            <p>+62 812 3456 7890</p>
                        </div>
                    </div>
                    <div class="footer-contact">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <p>info@azkaljayalas.com</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Azkal Jaya Las. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Change navbar background on scroll
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('.navbar').addClass('navbar-scrolled');
            } else {
                $('.navbar').removeClass('navbar-scrolled');
            }
        });

        // Smooth scrolling for navbar links
        $(document).ready(function() {
            $("a.nav-link").on('click', function(event) {
                if (this.hash !== "") {
                    event.preventDefault();
                    var hash = this.hash;
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top - 70
                    }, 800);
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>