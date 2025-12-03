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
        'produk',             // ML field: Pagar, Kanopi, Railing, Teralis, Pintu
        'jenis_produk',       // Legacy field (keep for compatibility)
        'jumlah_unit',        // ML field: Jumlah_Unit
        'jumlah_lubang',      // ML field: Jumlah_Lubang (nullable)
        'ukuran_m2',          // ML field: Ukuran_m2 (nullable)
        'jenis_material',     // ML field: Jenis_Material (Hollow, Besi, Stainless)
        'profile_size',       // Legacy field (keep for compatibility)
        'ketebalan_mm',       // ML field: Ketebalan_mm
        'finishing',          // ML field: Finishing (Cat, Powder Coating, Tanpa Finishing)
        'kerumitan_desain',   // ML field: Kerumitan_Desain (Sederhana, Menengah, Kompleks)
        'metode_hitung',      // ML field: Metode_Hitung (Per m², Per Lubang)
        'harga_akhir',        // ML prediction result
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
        'jumlah_lubang' => 'float',
        'ukuran_m2' => 'float',
        'ketebalan_mm' => 'float',
        'harga_akhir' => 'decimal:2',
    ];

    /**
     * Jenis produk yang tersedia (ML Format)
     */
    public static function jenisProduk(): array
    {
        return ['Pagar', 'Kanopi', 'Railing', 'Teralis', 'Pintu'];
    }

    /**
     * Jenis material yang tersedia (ML Format)
     */
    public static function jenisMaterial(): array
    {
        return ['Hollow', 'Besi', 'Stainless'];
    }

    /**
     * Jenis finishing yang tersedia (ML Format)
     */
    public static function finishingOptions(): array
    {
        return ['Cat', 'Powder Coating', 'Tanpa Finishing'];
    }

    /**
     * Kerumitan desain options (ML Format)
     */
    public static function kerumitanDesainOptions(): array
    {
        return ['Sederhana', 'Menengah', 'Kompleks'];
    }

    /**
     * Metode hitung options (ML Format)
     */
    public static function metodeHitungOptions(): array
    {
        return ['Per m²', 'Per Lubang'];
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