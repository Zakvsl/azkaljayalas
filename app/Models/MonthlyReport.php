<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'total_bookings',
        'confirmed_bookings',
        'cancelled_bookings',
        'completed_orders',
        'total_revenue',
        'total_dp_collected',
        'total_full_payment',
        'popular_products',
        'popular_materials',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'total_revenue' => 'decimal:2',
        'total_dp_collected' => 'decimal:2',
        'total_full_payment' => 'decimal:2',
        'popular_products' => 'array',
        'popular_materials' => 'array',
        'generated_at' => 'datetime',
    ];

    // Relationships
    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Helper methods
    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::create()->month($this->month)->translatedFormat('F');
    }

    public function getFormattedRevenueAttribute()
    {
        return 'Rp ' . number_format($this->total_revenue, 0, ',', '.');
    }

    public function getFormattedDpCollectedAttribute()
    {
        return 'Rp ' . number_format($this->total_dp_collected, 0, ',', '.');
    }

    public function getFormattedFullPaymentAttribute()
    {
        return 'Rp ' . number_format($this->total_full_payment, 0, ',', '.');
    }

    public function getCompletionRateAttribute()
    {
        if ($this->total_bookings == 0) return 0;
        return round(($this->completed_orders / $this->total_bookings) * 100, 2);
    }

    public function getCancellationRateAttribute()
    {
        if ($this->total_bookings == 0) return 0;
        return round(($this->cancelled_bookings / $this->total_bookings) * 100, 2);
    }
}
