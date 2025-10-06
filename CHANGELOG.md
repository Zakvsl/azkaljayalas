# Changelog - Azkal Jaya Las Web Application

## [2025-10-07] - Major Updates & Fixes

### ✅ Fixed Issues

#### 1. Authentication System

-   **Fixed**: Register form field mismatch
    -   Changed `phone` to `phone_number` in AuthController
    -   Now matches User model fillable fields
    -   Registration now saves data correctly to database

#### 2. Estimasi Harga Access

-   **Fixed**: Removed authentication requirement
    -   Users can now access estimasi harga form without login
    -   Direct access from homepage hero section
    -   Route: `/estimates/create` now public

#### 3. Database & Models

-   **Cleaned**: Removed duplicate Survey model
    -   Deleted `app/Models/Survey.php`
    -   Deleted duplicate migration files:
        -   `2024_01_15_000000_create_surveys_table.php`
        -   `2025_10_01_000000_create_surveys_table.php`
    -   Using only `SurveyBooking` model going forward

#### 4. Survey Booking Form

-   **Updated**: Form fields to match SurveyBooking model
    -   Changed from: tanggal, waktu, alamat, catatan
    -   Changed to: project_type, project_description, location, preferred_date, notes
    -   Added project type dropdown
    -   Better UX with placeholders and validation

### 🆕 New Features

#### 1. Admin Survey Management System

**Files Created:**

-   `app/Http/Controllers/Admin/SurveyBookingController.php`
-   `resources/views/admin/survey-bookings/index.blade.php`
-   `resources/views/admin/survey-bookings/show.blade.php`

**Features:**

-   List all survey bookings
-   Filter by status
-   Search by name/location/project type
-   Update booking status (pending → approved → completed → cancelled)
-   View detailed booking information
-   Delete bookings
-   Pagination support

#### 2. Machine Learning Integration

**Files Created:**

-   `ml/price_prediction.py` - Main ML script
-   `ml/requirements.txt` - Python dependencies
-   `ml/README.md` - ML documentation
-   `ml/DATASET_FORMAT.md` - Dataset specifications
-   `app/Services/MLPredictionService.php` - Laravel service
-   `app/Http/Controllers/Admin/MLModelController.php` - Admin controller
-   `resources/views/admin/ml/index.blade.php` - Admin UI

**Features:**

-   Random Forest Regressor for price prediction
-   Training model with historical data
-   Model persistence (saved as .pkl files)
-   Test prediction interface
-   Model metrics display (MAE, R²)
-   Python-Laravel integration

#### 3. Project Structure Improvements

**New Folders:**

```
ml/
├── models/          # Trained ML models
├── data/            # Dataset storage
└── .gitignore       # ML-specific ignores
```

**New Routes:**

-   `GET /admin/survey-bookings` - List bookings
-   `GET /admin/survey-bookings/{id}` - View booking
-   `PATCH /admin/survey-bookings/{id}/status` - Update status
-   `DELETE /admin/survey-bookings/{id}` - Delete booking
-   `GET /admin/ml` - ML management
-   `POST /admin/ml/train` - Train model
-   `POST /admin/ml/predict` - Test prediction

### 📝 Documentation Added

#### 1. INSTALLATION_GUIDE.md

-   Complete setup instructions
-   Step-by-step installation
-   Troubleshooting section
-   Testing checklist
-   Production deployment guide

#### 2. PROJECT_SUMMARY.md

-   Project overview
-   Feature descriptions
-   Folder structure
-   Technologies used
-   Database schema
-   API endpoints

#### 3. DEVELOPER_GUIDE.md

-   Quick start guide
-   Code structure
-   Development tips
-   Common issues & fixes
-   Testing flow

#### 4. ml/README.md

-   ML system documentation
-   Training instructions
-   Prediction usage
-   Integration guide

#### 5. ml/DATASET_FORMAT.md

-   Dataset specifications
-   Column definitions
-   Data validation rules
-   Examples & tips

### 🔧 Configuration Updates

#### .env.example

Added:

```env
APP_NAME="Azkal Jaya Las"
DB_DATABASE=azkaljayalas
PYTHON_PATH=python
```

### 🗂️ Code Organization

#### Clean Structure:

```
Controllers/
├── Auth/                    # Authentication
├── Admin/                   # Admin features
│   ├── DashboardController
│   ├── SurveyBookingController
│   └── MLModelController
├── SurveyController         # User booking
└── PriceEstimateController  # Price estimation

Services/
└── MLPredictionService      # ML integration

Models/
├── User
├── SurveyBooking
└── PriceEstimate
```

### 🔒 Security Improvements

-   Auth middleware properly applied
-   Admin middleware for sensitive routes
-   CSRF protection maintained
-   Input validation enhanced
-   SQL injection prevention

### 🎨 UI/UX Improvements

-   Survey booking form redesigned
-   Admin panel for booking management
-   ML management interface
-   Better error messages
-   Loading states
-   Responsive design maintained

### 🧪 Testing

#### Manual Testing Completed:

-   ✅ User registration with phone_number
-   ✅ User login
-   ✅ Public access to estimasi harga
-   ✅ Auth-protected booking survei
-   ✅ Admin booking management
-   ✅ Status updates
-   ✅ ML model training flow

### 📊 Database Changes

#### Migrations:

-   ✅ `create_survey_bookings_table` - Main booking table
-   ✅ Cleaned duplicate migrations
-   ✅ All migrations running successfully

### 🐛 Bug Fixes

1. Fixed phone vs phone_number field mismatch
2. Fixed auth middleware on estimate routes
3. Removed duplicate model files
4. Fixed form validation
5. Fixed database migration conflicts

### 🚀 Performance

-   No significant performance changes
-   ML prediction via Python subprocess
-   Efficient database queries with pagination
-   Proper indexing on foreign keys

### 📦 Dependencies

#### PHP (Composer):

-   No new dependencies

#### Python (pip):

```
pandas>=2.0.0
numpy>=1.24.0
scikit-learn>=1.3.0
joblib>=1.3.0
openpyxl>=3.1.0
```

### 🔄 Breaking Changes

-   **Survey Model**: Removed Survey.php, use SurveyBooking.php
-   **Form Fields**: Survey form fields changed
-   **Auth**: Estimasi harga now public (was protected)

### ⚠️ Known Issues

None currently

### 🎯 Next Steps / Future Enhancements

-   [ ] Email notifications for booking status
-   [ ] Real-time chat support
-   [ ] Mobile app
-   [ ] Payment gateway integration
-   [ ] Advanced analytics dashboard
-   [ ] Multi-language support
-   [ ] API endpoints for mobile
-   [ ] Image upload for projects
-   [ ] Customer dashboard
-   [ ] Invoice generation

### 👥 Contributors

-   Development Team

### 📄 License

Proprietary - Azkal Jaya Las

---

## Previous Versions

### [Initial Release] - Before 2025-10-07

-   Basic Laravel setup
-   User authentication
-   Simple homepage
-   Basic admin panel

---

**Last Updated**: October 7, 2025
