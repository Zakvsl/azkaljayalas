# Developer Quick Start Guide

## 🚀 Setup Cepat (5 Menit)

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
copy .env.example .env
php artisan key:generate

# 3. Database
# Edit .env, set DB_DATABASE=azkaljayalas
# Buat database di MySQL
php artisan migrate

# 4. Python ML
cd ml
pip install -r requirements.txt
cd ..

# 5. Create admin
php artisan tinker
# User::create(['name'=>'Admin','email'=>'admin@test.com','password'=>Hash::make('admin123'),'phone_number'=>'08123','address'=>'Test','role'=>'admin']);

# 6. Run
php artisan serve
```

## 📁 Struktur Penting

```
app/
├── Http/Controllers/
│   ├── Auth/AuthController.php          # Login/Register
│   ├── Admin/
│   │   ├── SurveyBookingController.php  # Admin kelola booking
│   │   └── MLModelController.php        # Admin kelola ML
│   ├── SurveyController.php             # User booking survei
│   └── PriceEstimateController.php      # Estimasi harga
├── Models/
│   ├── User.php                         # User model
│   ├── SurveyBooking.php                # Booking model
│   └── PriceEstimate.php                # Estimate model
└── Services/
    └── MLPredictionService.php          # ML service

resources/views/
├── welcome.blade.php                    # Homepage
├── auth/                                # Login/Register
├── survey/create.blade.php              # Form booking
└── admin/
    ├── survey-bookings/                 # Admin kelola booking
    └── ml/index.blade.php               # Admin ML

ml/
├── price_prediction.py                  # ML script
├── requirements.txt                     # Python deps
└── models/                              # Trained models
```

## 🔑 Key Routes

```php
// Public
GET  /                          # Homepage
GET  /estimates/create          # Form estimasi (no auth)
POST /estimates/calculate       # Calculate (no auth)

// Auth Required
GET  /survey/create             # Form booking
POST /survey/store              # Submit booking

// Admin Only
GET  /admin/survey-bookings     # List bookings
PATCH /admin/survey-bookings/{id}/status  # Update status
GET  /admin/ml                  # ML management
POST /admin/ml/train            # Train model
```

## 🎯 Fitur Utama

### 1. Auth System

-   Register/Login works ✅
-   Field: `phone_number` (bukan `phone`)
-   Auto role: `customer`

### 2. Estimasi Harga

-   **NO AUTH** required ✅
-   Direct access dari hero section
-   ML prediction

### 3. Booking Survei

-   **AUTH REQUIRED** ✅
-   Auto redirect ke login jika belum
-   Fields: project_type, description, location, date, notes

### 4. Admin Panel

-   Manage bookings
-   Update status: pending → approved → completed
-   ML training & testing

### 5. Machine Learning

-   Random Forest Regressor
-   Features: jenis_proyek, dimensi, material, kompleksitas
-   Python integration via exec()

## 🛠️ Development Tips

### Testing Flow

```bash
# Test register
POST /register
{
  name, email, password, password_confirmation,
  phone_number, address
}

# Test login
POST /login
{ email, password }

# Test estimate (no auth)
POST /estimates/calculate
{ jenis_proyek, panjang, lebar, tinggi, material, kompleksitas }

# Test booking (auth required)
POST /survey/store
{ project_type, project_description, location, preferred_date, notes }
```

### Common Issues

**Login/Register error**

-   Check `phone_number` field (not `phone`)
-   Check User model fillable

**Estimate not working**

-   Train ML model first
-   Check Python path in .env

**Booking redirect loop**

-   Check auth middleware
-   Verify session working

### Quick Fixes

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Regenerate autoload
composer dump-autoload

# Fix permissions
chmod -R 777 storage bootstrap/cache
```

## 🔧 Configuration

### .env Important

```env
APP_NAME="Azkal Jaya Las"
DB_DATABASE=azkaljayalas
PYTHON_PATH=python
```

### Model Relationship

```
User
  → hasMany SurveyBooking
  → hasMany PriceEstimate

SurveyBooking
  → belongsTo User
  → belongsTo PriceEstimate (nullable)
```

## 🎨 Frontend

-   **Tailwind CSS** - Styling
-   **Alpine.js** - Interactivity
-   **Blade** - Templating

### Key Views

```blade
// Homepage CTA
<a href="{{ route('estimates.create') }}">Estimasi Harga</a>
<a href="{{ route('survey.create') }}">Jadwalkan Survei</a>

// Auth Check
@auth
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
@else
  <a href="{{ route('login') }}">Login</a>
@endauth
```

## 📊 Database

### Users

```sql
id, name, email, password, phone_number, address, role
```

### Survey Bookings

```sql
id, user_id, project_type, project_description,
location, preferred_date, notes, status,
price_estimate_id, timestamps
```

### Price Estimates

```sql
id, user_id, project_type, dimensions, material,
complexity, estimated_price, timestamps
```

## 🤖 ML Integration

### Train Model

```php
$mlService = new MLPredictionService();
$results = $mlService->trainModel($datasetPath);
// Returns: { mae, r2, n_samples, features }
```

### Predict

```php
$features = [
    'jenis_proyek' => 'pagar',
    'panjang' => 10,
    'lebar' => 2,
    'tinggi' => 1.5,
    'material' => 'besi_hollow',
    'kompleksitas' => 'sedang'
];
$price = $mlService->predictPrice($features);
```

## 📝 Checklist Testing

-   [ ] Register new user
-   [ ] Login user
-   [ ] Access estimate without login
-   [ ] Get price prediction
-   [ ] Try booking without login (should redirect)
-   [ ] Login and access booking form
-   [ ] Submit booking
-   [ ] Login as admin
-   [ ] View bookings list
-   [ ] Update booking status
-   [ ] Train ML model
-   [ ] Test ML prediction

## 🚨 Production Ready

Before deploy:

```bash
# Optimize
php artisan optimize
php artisan config:cache
php artisan route:cache

# Set in .env
APP_ENV=production
APP_DEBUG=false
```

## 📚 Documentation

-   [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) - Full setup
-   [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md) - Project overview
-   [ml/README.md](ml/README.md) - ML documentation
-   [ml/DATASET_FORMAT.md](ml/DATASET_FORMAT.md) - Dataset format

---

**Happy Coding! 🚀**
