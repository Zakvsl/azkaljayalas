<?php

use App\Http\Controllers\PredictionController;
use App\Http\Controllers\SurveyBookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

// Prediction routes
Route::get('/prediction', [PredictionController::class, 'index'])->name('prediction.index');
Route::get('/prediction/create', [PredictionController::class, 'create'])->name('prediction.create');
Route::post('/prediction', [PredictionController::class, 'store'])->name('prediction.store');
Route::post('/prediction/calculate', [PredictionController::class, 'calculate'])->name('prediction.calculate');

// Survey Booking routes
Route::resource('survey-booking', SurveyBookingController::class);
