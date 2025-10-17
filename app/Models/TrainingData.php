<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingData extends Model
{
    protected $table = 'training_data';

    protected $fillable = [
        'produk',
        'jumlah_unit',
        'jumlah_lubang',
        'ukuran_m2',
        'jenis_material',
        'ketebalan_mm',
        'finishing',
        'kerumitan_desain',
        'metode_hitung',
        'harga_akhir',
        'notes'
    ];

    protected $casts = [
        'jumlah_unit' => 'integer',
        'jumlah_lubang' => 'float',
        'ukuran_m2' => 'float',
        'ketebalan_mm' => 'float',
        'harga_akhir' => 'decimal:2'
    ];

    // Format harga untuk display
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga_akhir, 0, ',', '.');
    }

    // Get badge color for product type
    public function getProdukBadgeAttribute()
    {
        $badges = [
            'Pagar' => 'bg-blue-100 text-blue-800',
            'Kanopi' => 'bg-green-100 text-green-800',
            'Railing' => 'bg-purple-100 text-purple-800',
            'Teralis' => 'bg-yellow-100 text-yellow-800',
            'Pintu' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->produk] ?? 'bg-gray-100 text-gray-800';
    }
}
