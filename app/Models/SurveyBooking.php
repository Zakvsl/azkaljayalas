<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'price_estimate_id',
        'project_type',
        'project_description',
        'location',
        'preferred_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'preferred_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priceEstimate()
    {
        return $this->belongsTo(PriceEstimate::class);
    }
}