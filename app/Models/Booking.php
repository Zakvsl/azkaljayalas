<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'prediction_id',
        'survey_date',
        'survey_time',
        'address',
        'notes',
        'status',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the prediction that owns the booking.
     */
    public function prediction(): BelongsTo
    {
        return $this->belongsTo(Prediction::class);
    }
}
