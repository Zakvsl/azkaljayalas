<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'survey_booking_id',
        'user_id',
        'survey_result_id',
        'customer_name',
        'phone',
        'address',
        'project_type',
        'material_type',
        'dimensions',
        'description',
        'total_price',
        'estimated_price',
        'actual_price',
        'dp_paid',
        'remaining_paid',
        'status',
        'progress_percentage',
        'current_stage',
        'progress_updates',
        'order_date',
        'completion_date',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'notes',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'progress_updates' => 'array',
        'total_price' => 'decimal:2',
        'estimated_price' => 'decimal:2',
        'actual_price' => 'decimal:2',
        'dp_paid' => 'decimal:2',
        'remaining_paid' => 'decimal:2',
        'progress_percentage' => 'integer',
        'order_date' => 'date',
        'completion_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
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

    public function surveyResult()
    {
        return $this->belongsTo(SurveyResult::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'survey_booking_id', 'survey_booking_id');
    }

    // Scopes
    public function scopePendingDp($query)
    {
        return $query->where('status', 'pending_dp');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Boot method to auto-generate order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', now())->count() + 1;
        return 'ORD-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // Helper methods
    public function getFormattedTotalPriceAttribute()
    {
        return $this->total_price ? 'Rp ' . number_format($this->total_price, 0, ',', '.') : '-';
    }

    public function getFormattedEstimatedPriceAttribute()
    {
        return $this->estimated_price ? 'Rp ' . number_format($this->estimated_price, 0, ',', '.') : '-';
    }

    public function getFormattedActualPriceAttribute()
    {
        return $this->actual_price ? 'Rp ' . number_format($this->actual_price, 0, ',', '.') : '-';
    }

    public function getFormattedDpPaidAttribute()
    {
        return $this->dp_paid ? 'Rp ' . number_format($this->dp_paid, 0, ',', '.') : '-';
    }

    public function isFullyPaid()
    {
        return ($this->dp_paid + $this->remaining_paid) >= $this->total_price;
    }

    public function addProgressUpdate($stage, $description, $percentage = null)
    {
        $updates = $this->progress_updates ?? [];
        $updates[] = [
            'stage' => $stage,
            'description' => $description,
            'percentage' => $percentage ?? $this->progress_percentage,
            'updated_at' => now()->toDateTimeString(),
        ];

        $this->update([
            'progress_updates' => $updates,
            'current_stage' => $stage,
            'progress_percentage' => $percentage ?? $this->progress_percentage,
        ]);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending_dp' => ['class' => 'badge-warning', 'text' => 'Menunggu DP'],
            'dp_pending_confirm' => ['class' => 'badge-info', 'text' => 'DP Menunggu Konfirmasi'],
            'in_progress' => ['class' => 'badge-primary', 'text' => 'Dalam Pengerjaan'],
            'ready_for_pickup' => ['class' => 'badge-success', 'text' => 'Siap Diambil'],
            'completed' => ['class' => 'badge-success', 'text' => 'Selesai'],
            'cancelled' => ['class' => 'badge-danger', 'text' => 'Dibatalkan'],
            default => ['class' => 'badge-secondary', 'text' => 'Unknown'],
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending_dp' => 'Menunggu DP',
            'dp_pending_confirm' => 'DP Menunggu Konfirmasi',
            'in_progress' => 'Dalam Pengerjaan',
            'ready_for_pickup' => 'Siap Diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
