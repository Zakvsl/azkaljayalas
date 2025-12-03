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
        'whatsapp_number',
        'latitude',
        'longitude',
        'preferred_date',
        'preferred_time',
        'status',
        'notes',
        'cancel_reason',
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

    public function surveyResult()
    {
        return $this->hasOne(SurveyResult::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}