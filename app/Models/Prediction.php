<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    protected $fillable = [
        'product_id',
        'material_id',
        'finishing_id',
        'kerumitan_id',
        'ketebalan_id',
        'width',
        'height',
        'length',
        'quantity',
        'total_price',
        'user_id',
        'status',
    ];

    /**
     * Get the product that owns the prediction.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the material that owns the prediction.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the finishing that owns the prediction.
     */
    public function finishing(): BelongsTo
    {
        return $this->belongsTo(Finishing::class);
    }

    /**
     * Get the kerumitan that owns the prediction.
     */
    public function kerumitan(): BelongsTo
    {
        return $this->belongsTo(Kerumitan::class);
    }

    /**
     * Get the ketebalan that owns the prediction.
     */
    public function ketebalan(): BelongsTo
    {
        return $this->belongsTo(Ketebalan::class);
    }

    /**
     * Get the user that owns the prediction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
