<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SurveyBookingController;
use App\Http\Controllers\Admin\MLModelController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PriceEstimateController as AdminPriceEstimateController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\TrainingDataController as AdminTrainingDataController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\PriceEstimateController;
use App\Http\Controllers\PortfolioController;
use App\Models\PriceEstimate;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Include additional route files
require __DIR__ . '/web/price-estimates.php';
require __DIR__ . '/web/ml.php';

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Portfolio Routes
Route::get('/portfolio/{category?}', [PortfolioController::class, 'index'])->name('portfolio.index');

// Survey Routes
Route::prefix('survey')->group(function () {
    Route::get('/create', [SurveyController::class, 'create'])->name('survey.create');
    Route::post('/store', [SurveyController::class, 'store'])->name('survey.store');
});

// Estimate routes are defined in routes/web/price-estimates.php

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', [AuthController::class, 'register']);

    // Password Reset Routes
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])
        ->name('password.reset');

    Route::post('/reset-password', [PasswordResetController::class, 'update'])
        ->name('password.store');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Email Verification Routes
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/')->with('success', 'Email verified successfully!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');

    // Profile Routes (untuk customer/user biasa)
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');
        
        // Admin Price Estimates Management
        Route::prefix('admin/estimates')->name('admin.estimates.')->group(function () {
            Route::get('/', [AdminPriceEstimateController::class, 'index'])->name('index');
            Route::get('/{estimate}', [AdminPriceEstimateController::class, 'show'])->name('show');
            Route::patch('/{estimate}', [AdminPriceEstimateController::class, 'update'])->name('update');
            Route::delete('/{estimate}', [AdminPriceEstimateController::class, 'destroy'])->name('destroy');
        });
        
        // Admin Profile Management
        Route::prefix('admin/profile')->name('admin.profile.')->group(function () {
            Route::get('/', [AdminProfileController::class, 'edit'])->name('edit');
            Route::patch('/', [AdminProfileController::class, 'update'])->name('update');
            Route::put('/password', [AdminProfileController::class, 'updatePassword'])->name('password.update');
        });
        
        // Orders Management
        Route::prefix('admin/orders')->name('admin.orders.')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            Route::get('/create', [AdminOrderController::class, 'create'])->name('create');
            Route::post('/', [AdminOrderController::class, 'store'])->name('store');
            Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
            Route::get('/{order}/edit', [AdminOrderController::class, 'edit'])->name('edit');
            Route::patch('/{order}', [AdminOrderController::class, 'update'])->name('update');
            Route::delete('/{order}', [AdminOrderController::class, 'destroy'])->name('destroy');
        });

        // Training Data Management
        Route::prefix('admin/training-data')->name('admin.training-data.')->group(function () {
            Route::get('/', [AdminTrainingDataController::class, 'index'])->name('index');
            Route::get('/create', [AdminTrainingDataController::class, 'create'])->name('create');
            Route::get('/export/csv', [AdminTrainingDataController::class, 'export'])->name('export');
            Route::get('/import', [AdminTrainingDataController::class, 'importForm'])->name('import-form');
            Route::post('/import', [AdminTrainingDataController::class, 'import'])->name('import');
            Route::delete('/delete-all', [AdminTrainingDataController::class, 'deleteAll'])->name('delete-all');
            Route::post('/', [AdminTrainingDataController::class, 'store'])->name('store');
            Route::get('/{trainingDatum}', [AdminTrainingDataController::class, 'show'])->name('show');
            Route::get('/{trainingDatum}/edit', [AdminTrainingDataController::class, 'edit'])->name('edit');
            Route::patch('/{trainingDatum}', [AdminTrainingDataController::class, 'update'])->name('update');
            Route::delete('/{trainingDatum}', [AdminTrainingDataController::class, 'destroy'])->name('destroy');
        });
        
        // Survey Bookings Management
        Route::prefix('admin/survey-bookings')->name('admin.survey-bookings.')->group(function () {
            Route::get('/', [SurveyBookingController::class, 'index'])->name('index');
            Route::get('/{surveyBooking}', [SurveyBookingController::class, 'show'])->name('show');
            Route::patch('/{surveyBooking}/status', [SurveyBookingController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{surveyBooking}', [SurveyBookingController::class, 'destroy'])->name('destroy');
        });

        // ML Model Management
        Route::prefix('admin/ml')->name('admin.ml.')->group(function () {
            Route::get('/', [MLModelController::class, 'index'])->name('index');
            Route::post('/train', [MLModelController::class, 'train'])->name('train');
            Route::post('/retrain', [MLModelController::class, 'retrain'])->name('retrain');
            Route::post('/predict', [MLModelController::class, 'testPrediction'])->name('predict');
            Route::get('/download-metrics', [MLModelController::class, 'downloadMetrics'])->name('download-metrics');
            Route::get('/download-features', [MLModelController::class, 'downloadFeatureImportances'])->name('download-features');
        });

        // User Management
        Route::resource('admin/users', UserController::class)->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'show' => 'admin.users.show',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);
    });
});
