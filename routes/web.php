<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SurveyBookingController;
use App\Http\Controllers\Admin\MLModelController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\PriceEstimateController;
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

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');
        
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
            Route::post('/predict', [MLModelController::class, 'testPrediction'])->name('predict');
        });
    });
});
