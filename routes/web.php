<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MLModelController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PriceEstimateController as AdminPriceEstimateController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\TrainingDataController as AdminTrainingDataController;
use App\Http\Controllers\Admin\AdminSurveyController;
use App\Http\Controllers\Admin\SurveyBookingController as AdminSurveyBookingController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminOrderController as NewAdminOrderController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\PriceEstimateController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SurveyBookingController as UserSurveyBookingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController as UserOrderController;
use App\Http\Controllers\HistoryController;
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

// Survey Routes (LEGACY - Redirect to survey-booking)
Route::prefix('survey')->group(function () {
    Route::get('/create', function(\Illuminate\Http\Request $request) {
        return redirect()->route('survey-booking.create', $request->query());
    })->name('survey.create');
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

    // Survey Booking Routes (User)
    Route::get('survey-booking/create', [UserSurveyBookingController::class, 'create'])->name('survey-booking.create');
    Route::get('survey-booking/available-slots', [UserSurveyBookingController::class, 'getAvailableSlots'])->name('survey-booking.available-slots');
    Route::post('survey-booking', [UserSurveyBookingController::class, 'store'])->name('survey-booking.store');
    Route::get('survey-booking', [UserSurveyBookingController::class, 'index'])->name('survey-booking.index');
    Route::get('survey-booking/{booking}', [UserSurveyBookingController::class, 'show'])->name('survey-booking.show');
    Route::get('survey-booking/{booking}/edit', [UserSurveyBookingController::class, 'edit'])->name('survey-booking.edit');
    Route::put('survey-booking/{booking}', [UserSurveyBookingController::class, 'update'])->name('survey-booking.update');
    Route::delete('survey-booking/{booking}', [UserSurveyBookingController::class, 'destroy'])->name('survey-booking.destroy');
    Route::get('survey-booking/{booking}/price-offer', [UserSurveyBookingController::class, 'showPriceOffer'])->name('survey-booking.price-offer');
    Route::post('survey-booking/{booking}/accept-price', [UserSurveyBookingController::class, 'acceptPrice'])->name('survey-booking.accept-price');
    Route::post('survey-booking/{booking}/reject-price', [UserSurveyBookingController::class, 'rejectPrice'])->name('survey-booking.reject-price');

    // Notification Routes (User)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/delete-all-read', [NotificationController::class, 'deleteAllRead'])->name('delete-all-read');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/latest', [NotificationController::class, 'latest'])->name('latest');
    });

    // History / Riwayat (User)
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

    // Payment Routes (User)
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create', [PaymentController::class, 'create'])->name('create');
        Route::post('/{payment}', [PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/remaining', [PaymentController::class, 'uploadRemaining'])->name('upload-remaining');
        Route::get('/history', [PaymentController::class, 'history'])->name('history');
    });

    // Order Routes (User)
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [UserOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [UserOrderController::class, 'show'])->name('show');
        Route::get('/{order}/invoice', [UserOrderController::class, 'invoice'])->name('invoice');
        Route::get('/history', [UserOrderController::class, 'history'])->name('history');
        Route::post('/{order}/cancel', [UserOrderController::class, 'cancel'])->name('cancel');
    });
    Route::get('/track-order', [UserOrderController::class, 'track'])->name('orders.track');

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
            Route::get('/', [AdminSurveyBookingController::class, 'index'])->name('index');
            Route::get('/{surveyBooking}', [AdminSurveyBookingController::class, 'show'])->name('show');
            Route::patch('/{surveyBooking}/status', [AdminSurveyBookingController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{surveyBooking}', [AdminSurveyBookingController::class, 'destroy'])->name('destroy');
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

        // NEW Survey Management Routes (Admin)
        Route::prefix('admin/survey-bookings')->name('admin.survey-bookings.')->group(function () {
            Route::get('/', [AdminSurveyController::class, 'index'])->name('index');
            Route::get('/all', [AdminSurveyController::class, 'allBookings'])->name('all');
            Route::get('/{booking}', [AdminSurveyController::class, 'show'])->name('show');
            Route::patch('/{booking}/update-status', [AdminSurveyController::class, 'updateStatus'])->name('update-status');
            Route::post('/{booking}/confirm', [AdminSurveyController::class, 'confirm'])->name('confirm');
            Route::post('/{booking}/cancel', [AdminSurveyController::class, 'cancel'])->name('cancel');
            Route::get('/{booking}/survey-form', [AdminSurveyController::class, 'surveyForm'])->name('form');
            Route::post('/{booking}/survey-result', [AdminSurveyController::class, 'storeSurveyResult'])->name('store-result');
            Route::get('/{booking}/adjust-price', [AdminSurveyController::class, 'adjustPriceForm'])->name('adjust-price');
            Route::post('/{booking}/send-offer', [AdminSurveyController::class, 'sendPriceOffer'])->name('send-offer');
            Route::delete('/{booking}', [AdminSurveyController::class, 'destroy'])->name('destroy');
        });

        // Admin Notifications Routes
        Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
            Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
            Route::get('/unread-count', [AdminNotificationController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/recent', [AdminNotificationController::class, 'getRecent'])->name('recent');
        });

        // NEW Payment Management Routes (Admin)
        Route::prefix('admin/payments')->name('admin.payments.')->group(function () {
            Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
            Route::get('/all', [AdminPaymentController::class, 'allPayments'])->name('all');
            Route::get('/{payment}', [AdminPaymentController::class, 'show'])->name('show');
            Route::post('/{payment}/confirm', [AdminPaymentController::class, 'confirm'])->name('confirm');
            Route::post('/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('reject');
        });

        // NEW Order Management Routes (Admin)
        Route::prefix('admin/order-management')->name('admin.order-management.')->group(function () {
            Route::get('/', [NewAdminOrderController::class, 'index'])->name('index');
            Route::get('/all', [NewAdminOrderController::class, 'allOrders'])->name('all');
            Route::get('/{order}', [NewAdminOrderController::class, 'show'])->name('show');
            Route::post('/{order}/update-progress', [NewAdminOrderController::class, 'updateProgress'])->name('update-progress');
            Route::post('/{order}/start-production', [NewAdminOrderController::class, 'startProduction'])->name('start-production');
            Route::post('/{order}/mark-ready', [NewAdminOrderController::class, 'markReady'])->name('mark-ready');
            Route::post('/{order}/mark-completed', [NewAdminOrderController::class, 'markCompleted'])->name('mark-completed');
            Route::post('/{order}/cancel', [NewAdminOrderController::class, 'cancel'])->name('cancel');
        });

        // Monthly Reports Routes (Admin)
        Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
            Route::get('/', [AdminReportController::class, 'index'])->name('index');
            Route::get('/{report}', [AdminReportController::class, 'show'])->name('show');
            Route::post('/generate', [AdminReportController::class, 'generate'])->name('generate');
        });
    });
});