<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyBooking extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'survey_date',
        'survey_time',
        'notes',
        'product_id',
        'material_id',
        'finishing_id',
        'kerumitan_id',
        'ketebalan_id',
        'width',
        'height',
        'length',
        'quantity',
        'status',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'survey_date' => 'date',
        'survey_time' => 'datetime',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'length' => 'decimal:2',
    ];
    
    /**
     * Get the product that owns the survey booking.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the material that owns the survey booking.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
    
    /**
     * Get the finishing that owns the survey booking.
     */
    public function finishing(): BelongsTo
    {
        return $this->belongsTo(Finishing::class);
    }
    
    /**
     * Get the kerumitan that owns the survey booking.
     */
    public function kerumitan(): BelongsTo
    {
        return $this->belongsTo(Kerumitan::class);
    }
    
    /**
     * Get the ketebalan that owns the survey booking.
     */
    public function ketebalan(): BelongsTo
    {
        return $this->belongsTo(Ketebalan::class);
    }
}
