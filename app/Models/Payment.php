<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_booking_id',
        'user_id',
        'payment_type',
        'total_price',
        'dp_amount',
        'dp_percentage',
        'remaining_amount',
        'paid_amount',
        'status',
        'payment_proof',
        'paid_at',
        'confirmed_by',
        'confirmed_at',
        'rejection_reason',
        'payment_method',
        'payment_notes',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'dp_percentage' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    // Relationships
    public function surveyBooking()
    {
        return $this->belongsTo(SurveyBooking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeWaitingConfirmation($query)
    {
        return $query->where('status', 'waiting_confirmation');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    // Helper methods
    public function calculateDpAmount()
    {
        if ($this->dp_percentage) {
            return ($this->total_price * $this->dp_percentage) / 100;
        }
        return $this->dp_amount ?? 0;
    }

    public function calculateRemainingAmount()
    {
        $dp = $this->calculateDpAmount();
        return $this->total_price - $dp;
    }

    public function isFullyPaid()
    {
        return $this->paid_amount >= $this->total_price;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => ['class' => 'badge-secondary', 'text' => 'Menunggu'],
            'waiting_confirmation' => ['class' => 'badge-warning', 'text' => 'Menunggu Konfirmasi'],
            'confirmed' => ['class' => 'badge-success', 'text' => 'Terkonfirmasi'],
            'rejected' => ['class' => 'badge-danger', 'text' => 'Ditolak'],
            default => ['class' => 'badge-secondary', 'text' => 'Unknown'],
        };
    }
}
