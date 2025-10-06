# Azkal Jaya Las - Web Application

## Overview

Aplikasi web untuk bengkel las yang menyediakan fitur estimasi harga menggunakan Machine Learning (Random Forest), manajemen booking survei, dan panel admin.

## Fitur Utama

### 1. **Homepage (Public)**

-   Hero section dengan CTA untuk estimasi harga dan booking survei
-   Layanan yang ditawarkan
-   Portfolio/galeri proyek
-   Testimoni pelanggan
-   Informasi kontak

### 2. **Authentication System**

-   Register customer baru
-   Login/Logout
-   Profile management
-   Password reset

### 3. **Estimasi Harga (Public - No Auth Required)**

-   Form estimasi harga tanpa perlu login
-   Prediksi harga menggunakan ML Random Forest
-   Parameter: jenis proyek, dimensi, material, kompleksitas
-   Real-time prediction

### 4. **Booking Survei (Auth Required)**

-   User harus login terlebih dahulu
-   Form booking dengan field:
    -   Jenis proyek
    -   Deskripsi proyek
    -   Lokasi survei
    -   Tanggal yang diinginkan
    -   Catatan tambahan
-   Status tracking: pending, approved, completed, cancelled

### 5. **Admin Panel**

-   Dashboard overview
-   Manajemen booking survei:
    -   List semua booking
    -   Filter by status
    -   Search by name/location/project type
    -   Update status booking
    -   Detail view booking
    -   Delete booking
-   ML Model Management:
    -   Training model
    -   View model metrics (MAE, R²)
    -   Test prediction
    -   Model status

### 6. **Machine Learning System**

-   Random Forest Regressor untuk prediksi harga
-   Training dengan dataset historis
-   Features: jenis_proyek, dimensi (p/l/t), material, kompleksitas
-   Python integration dengan Laravel
-   Model persistence (saved as .pkl files)

## Struktur Folder

```
azkaljayav2/azkaljayalas2/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/
│   │       │   ├── AuthController.php
│   │       │   └── PasswordResetController.php
│   │       ├── Admin/
│   │       │   ├── DashboardController.php
│   │       │   ├── SurveyBookingController.php
│   │       │   └── MLModelController.php
│   │       ├── SurveyController.php
│   │       └── PriceEstimateController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── SurveyBooking.php
│   │   └── PriceEstimate.php
│   └── Services/
│       └── MLPredictionService.php
├── database/
│   └── migrations/
│       ├── create_users_table.php
│       ├── create_survey_bookings_table.php
│       └── create_price_estimates_table.php
├── resources/
│   └── views/
│       ├── welcome.blade.php (Homepage)
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── survey/
│       │   └── create.blade.php
│       ├── estimates/
│       │   └── create.blade.php
│       └── admin/
│           ├── dashboard.blade.php
│           ├── survey-bookings/
│           │   ├── index.blade.php
│           │   └── show.blade.php
│           └── ml/
│               └── index.blade.php
├── routes/
│   ├── web.php
│   └── web/
│       ├── price-estimates.php
│       └── ml.php
├── ml/ (Machine Learning)
│   ├── price_prediction.py
│   ├── requirements.txt
│   ├── README.md
│   ├── models/ (generated after training)
│   │   ├── price_model.pkl
│   │   └── label_encoders.pkl
│   └── data/
└── dataset_transaksi_bengkel_las_130.xlsx
```

## Setup Installation

### 1. Clone & Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env`:

```env
APP_NAME="Azkal Jaya Las"
DB_DATABASE=azkaljayalas
DB_USERNAME=root
DB_PASSWORD=

PYTHON_PATH=python
```

### 3. Database Setup

```bash
php artisan migrate
```

### 4. Python ML Setup

```bash
cd ml
pip install -r requirements.txt
```

### 5. Create Admin User

```bash
php artisan tinker
```

```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@azkaljayalas.com',
    'password' => Hash::make('password'),
    'phone_number' => '081234567890',
    'address' => 'Alamat Admin',
    'role' => 'admin'
]);
```

### 6. Train ML Model

-   Login sebagai admin
-   Navigate to "ML Model Management"
-   Click "Train Model"
-   Wait for training to complete

## User Flow

### Customer Flow

1. Visit homepage
2. Klik "Estimasi Harga" → Langsung ke form (no auth)
3. Input parameter proyek
4. Dapatkan estimasi harga
5. Jika ingin booking survei → Klik "Jadwalkan Survei"
6. Redirect ke login jika belum login
7. Setelah login → Redirect ke form booking survei
8. Submit booking → Status pending
9. Dapat notifikasi saat status berubah

### Admin Flow

1. Login sebagai admin
2. Dashboard → Overview statistics
3. Survey Bookings → Manage all bookings
4. Filter/Search bookings
5. Update status (pending → approved → completed)
6. ML Management → Train/Test model

## API Endpoints

### Public

-   `GET /` - Homepage
-   `GET /estimates/create` - Form estimasi (no auth)
-   `POST /estimates/calculate` - Calculate estimate

### Authenticated

-   `GET /survey/create` - Form booking survei
-   `POST /survey/store` - Submit booking

### Admin Only

-   `GET /admin/dashboard`
-   `GET /admin/survey-bookings`
-   `PATCH /admin/survey-bookings/{id}/status`
-   `GET /admin/ml`
-   `POST /admin/ml/train`
-   `POST /admin/ml/predict`

## Technologies

### Backend

-   Laravel 11
-   PHP 8.2+
-   MySQL
-   Python 3.8+ (ML)

### Frontend

-   Tailwind CSS
-   Alpine.js
-   Blade Templates

### Machine Learning

-   Python scikit-learn
-   pandas, numpy
-   Random Forest Regressor
-   joblib (model persistence)

## Database Schema

### users

-   id, name, email, password
-   phone_number, address
-   role (customer/admin)
-   timestamps

### survey_bookings

-   id, user_id
-   project_type, project_description
-   location, preferred_date
-   notes, status
-   price_estimate_id (nullable)
-   timestamps

### price_estimates

-   id, user_id (nullable)
-   project_type, dimensions
-   material, complexity
-   estimated_price
-   timestamps

## Security Features

-   CSRF protection
-   SQL injection prevention
-   XSS protection
-   Authentication middleware
-   Admin role checking
-   Input validation

## Development Notes

### Fixed Issues

1. ✅ Register/Login field mismatch (`phone` → `phone_number`)
2. ✅ Removed auth middleware from price estimates
3. ✅ Cleaned up duplicate Survey model
4. ✅ Updated SurveyBooking form fields
5. ✅ Created complete admin management
6. ✅ Integrated ML prediction system

### Code Quality

-   Clean folder structure
-   Proper naming conventions
-   Separated concerns (Controllers, Services, Models)
-   Reusable components
-   Well-documented code

## Future Enhancements

-   Email notifications
-   Real-time chat support
-   Mobile app
-   Payment gateway integration
-   Advanced reporting & analytics
-   Multi-language support

## Support

Untuk pertanyaan atau issue, hubungi tim development.
