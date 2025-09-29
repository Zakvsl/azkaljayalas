<?php

use App\Http\Controllers\MLController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Model Metrics Page
    Route::get('/admin/ml/metrics', function () {
        return view('admin.model-metrics');
    })->middleware('role:admin')->name('admin.ml.metrics');

    // ML Model Management (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/api/ml/status', [MLController::class, 'getModelStatus'])
            ->name('ml.status');
        Route::get('/api/ml/history', [MLController::class, 'getTrainingHistory'])
            ->name('ml.history');
        Route::post('/api/ml/retrain', [MLController::class, 'retrainModel'])
            ->name('ml.retrain');
        Route::post('/api/ml/rollback/{version}', [MLController::class, 'rollbackModel'])
            ->name('ml.rollback');
    });
    
    // Price Estimation (authenticated users)
    Route::post('/api/ml/estimate', [MLController::class, 'getPriceEstimate'])
        ->name('ml.estimate');
});