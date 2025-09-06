<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'base_price',
        'image',
        'is_active',
    ];

    /**
     * Get the predictions for the product.
     */
    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }
    
    /**
     * Get the survey bookings for the product.
     */
    public function surveyBookings(): HasMany
    {
        return $this->hasMany(SurveyBooking::class);
    }
}
