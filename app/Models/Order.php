<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'phone',
        'address',
        'project_type',
        'material_type',
        'dimensions',
        'description',
        'estimated_price',
        'actual_price',
        'status',
        'notes',
        'order_date',
        'completion_date',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'estimated_price' => 'decimal:2',
        'actual_price' => 'decimal:2',
        'order_date' => 'date',
        'completion_date' => 'date',
    ];

    // Accessor for formatted estimated price
    public function getFormattedEstimatedPriceAttribute()
    {
        return $this->estimated_price ? 'Rp ' . number_format($this->estimated_price, 0, ',', '.') : '-';
    }

    // Accessor for formatted actual price
    public function getFormattedActualPriceAttribute()
    {
        return $this->actual_price ? 'Rp ' . number_format($this->actual_price, 0, ',', '.') : '-';
    }

    // Status badges
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'in_progress' => 'Dalam Proses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
