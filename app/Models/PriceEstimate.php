<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PriceEstimate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'project_type',
        'material_type',
        'dimensions',
        'additional_features',
        'estimated_price',
        'actual_price',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dimensions' => 'json',
        'additional_features' => 'json',
        'estimated_price' => 'decimal:2',
        'actual_price' => 'decimal:2',
    ];

    /**
     * Get the status values available.
     *
     * @return array<string>
     */
    public static function statusValues(): array
    {
        return ['pending', 'confirmed', 'rejected'];
    }

    /**
     * Get the user that owns the price estimate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the survey booking associated with the price estimate.
     */
    public function surveyBooking(): HasOne
    {
        return $this->hasOne(SurveyBooking::class);
    }

    /**
     * Scope a query to only include pending estimates.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed estimates.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include rejected estimates.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}