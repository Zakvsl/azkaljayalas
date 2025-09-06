<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Finishing extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price_per_unit',
        'unit',
        'is_active',
    ];

    /**
     * Get the predictions for the finishing.
     */
    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }
}
