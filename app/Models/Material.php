<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price_per_unit',
        'unit',
        'is_active',
    ];

    /**
     * Get the predictions for the material.
     */
    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }
    
    /**
     * Get the survey bookings for the material.
     */
    public function surveyBookings(): HasMany
    {
        return $this->hasMany(SurveyBooking::class);
    }
}
