<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_booking_id',
        'surveyed_by',
        'product_id',
        'material_id',
        'finishing_id',
        'kerumitan_id',
        'ketebalan_id',
        'width',
        'height',
        'length',
        'quantity',
        'ai_estimated_price',
        'admin_adjusted_price',
        'final_price',
        'survey_notes',
        'survey_photos',
        'special_requirements',
        'surveyed_at',
    ];

    protected $casts = [
        'survey_photos' => 'array',
        'surveyed_at' => 'datetime',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'length' => 'decimal:2',
        'ai_estimated_price' => 'decimal:2',
        'admin_adjusted_price' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    // Relationships
    public function surveyBooking()
    {
        return $this->belongsTo(SurveyBooking::class);
    }

    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyed_by');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function finishing()
    {
        return $this->belongsTo(Finishing::class);
    }

    public function kerumitan()
    {
        return $this->belongsTo(Kerumitan::class);
    }

    public function ketebalan()
    {
        return $this->belongsTo(Ketebalan::class);
    }

    // Helper methods
    public function getFinalPriceAttribute($value)
    {
        return $value ?? $this->admin_adjusted_price ?? $this->ai_estimated_price;
    }
}
