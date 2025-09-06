<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ketebalan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'value',
        'unit',
        'is_active',
    ];

    /**
     * Get the predictions for the ketebalan.
     */
    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }
}
