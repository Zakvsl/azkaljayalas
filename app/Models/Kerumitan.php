<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kerumitan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'multiplier',
        'is_active',
    ];

    /**
     * Get the predictions for the kerumitan.
     */
    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }
}
