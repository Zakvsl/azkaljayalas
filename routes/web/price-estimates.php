<?php

use App\Http\Controllers\PriceEstimateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/estimates/create', [PriceEstimateController::class, 'create'])->name('estimates.create');
    Route::post('/estimates/calculate', [PriceEstimateController::class, 'estimate'])->name('estimates.calculate');
    Route::post('/estimates', [PriceEstimateController::class, 'store'])->name('estimates.store');
    Route::get('/estimates', [PriceEstimateController::class, 'index'])->name('estimates.index');
    Route::get('/estimates/{estimate}', [PriceEstimateController::class, 'show'])->name('estimates.show');
    
    // Admin only routes
    Route::middleware(['admin'])->group(function () {
        Route::patch('/estimates/{estimate}', [PriceEstimateController::class, 'update'])->name('estimates.update');
    });
});