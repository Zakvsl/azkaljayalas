<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'survey_booking_id',
        'order_id',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function surveyBooking()
    {
        return $this->belongsTo(SurveyBooking::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getIconAttribute()
    {
        return match($this->type) {
            'booking' => '📅',
            'survey' => '📍',
            'price_offer' => '💰',
            'price_accepted' => '✅',
            'price_rejected' => '❌',
            'order_progress' => '🔧',
            'payment' => '💳',
            default => '🔔',
        };
    }

    public function getColorAttribute()
    {
        return match($this->type) {
            'booking' => 'bg-blue-100 text-blue-600',
            'survey' => 'bg-indigo-100 text-indigo-600',
            'price_offer' => 'bg-yellow-100 text-yellow-600',
            'price_accepted' => 'bg-green-100 text-green-600',
            'price_rejected' => 'bg-red-100 text-red-600',
            'order_progress' => 'bg-purple-100 text-purple-600',
            'payment' => 'bg-pink-100 text-pink-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}
