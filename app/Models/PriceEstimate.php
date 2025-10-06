<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PriceEstimate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'jenis_produk',
        'jumlah_unit',
        'jumlah_lubang',      // nullable - hanya untuk Teralis
        'ukuran_m2',          // nullable - untuk non-Teralis
        'jenis_material',
        'profile_size',       // nullable - tidak untuk plat
        'ketebalan_mm',
        'finishing',
        'kerumitan_desain',
        'harga_akhir',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jumlah_unit' => 'integer',
        'jumlah_lubang' => 'integer',
        'ukuran_m2' => 'decimal:2',
        'ketebalan_mm' => 'decimal:2',
        'kerumitan_desain' => 'integer',
        'harga_akhir' => 'decimal:2',
    ];

    /**
     * Jenis produk yang tersedia
     */
    public static function jenisProduk(): array
    {
        return ['Pagar', 'Kanopi', 'Railing', 'Teralis', 'Pintu', 'Tangga'];
    }

    /**
     * Jenis material yang tersedia
     */
    public static function jenisMaterial(): array
    {
        return ['hollow', 'besi_siku', 'aluminium', 'stainless', 'plat'];
    }

    /**
     * Jenis finishing yang tersedia
     */
    public static function finishingOptions(): array
    {
        return ['cat_biasa', 'cat_epoxy', 'powder_coating', 'galvanis'];
    }

    /**
     * Get the status values available.
     *
     * @return array<string>
     */
    public static function statusValues(): array
    {
        return ['pending', 'confirmed', 'rejected'];
    }

    /**
     * Get the user that owns the price estimate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the survey booking associated with the price estimate.
     */
    public function surveyBooking(): HasOne
    {
        return $this->hasOne(SurveyBooking::class);
    }

    /**
     * Scope a query to only include pending estimates.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed estimates.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include rejected estimates.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}