<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PriceEstimationService
{
    protected MLPredictionService $mlService;

    public function __construct(MLPredictionService $mlService)
    {
        $this->mlService = $mlService;
    }

    public function predictPrice(array $data)
    {
        try {
            // Map form data ke format ML model
            $mlData = $this->mapFormDataToMLFormat($data);
            
            // Coba gunakan ML prediction
            if ($this->mlService->isModelTrained()) {
                try {
                    $prediction = $this->mlService->predict($mlData);
                    return $prediction['predicted_price'];
                } catch (\Exception $e) {
                    Log::warning('ML prediction failed, using fallback calculation', [
                        'error' => $e->getMessage(),
                        'data' => $mlData
                    ]);
                }
            }
            
            // Fallback ke formula sederhana jika ML gagal
            return $this->calculateFallbackPrice($data);

        } catch (\Exception $e) {
            Log::error('Price prediction failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new \Exception('Gagal menghitung harga: ' . $e->getMessage());
        }
    }

    /**
     * Map form data to ML model format
     */
    public function mapFormDataToMLFormat(array $data): array
    {
        // Map jenis_material dari form format (lowercase underscore) ke ML format (title case)
        $materialMap = [
            'hollow' => 'Hollow',
            'besi_siku' => 'Besi',
            'aluminium' => 'Besi', // Map aluminium ke Besi karena model tidak punya kategori ini
            'stainless' => 'Stainless',
            'plat' => 'Besi',
        ];

        // Map finishing dari form format ke ML format
        $finishingMap = [
            'cat_biasa' => 'Cat',
            'cat_epoxy' => 'Cat',
            'powder_coating' => 'Powder Coating',
            'galvanis' => 'Tanpa Finishing',
        ];

        // Map kerumitan_desain dari angka ke text
        $complexityMap = [
            1 => 'Sederhana',
            2 => 'Menengah',
            3 => 'Kompleks',
        ];

        // Support both jenis_produk and produk (backward compat)
        $produk = $data['jenis_produk'] ?? $data['produk'] ?? 'Pagar';
        
        // Tentukan metode_hitung berdasarkan jenis_produk
        $metodeHitung = ($produk === 'Teralis') ? 'Per Lubang' : 'Per m²';

        return [
            'produk' => $produk,
            'jumlah_unit' => (int)$data['jumlah_unit'],
            'jumlah_lubang' => (float)($data['jumlah_lubang'] ?? 0),
            'ukuran_m2' => (float)($data['ukuran_m2'] ?? 0),
            'jenis_material' => $materialMap[$data['jenis_material']] ?? 'Hollow',
            'ketebalan_mm' => (float)$data['ketebalan_mm'],
            'finishing' => $finishingMap[$data['finishing']] ?? 'Cat',
            'kerumitan_desain' => $complexityMap[$data['kerumitan_desain']] ?? 'Sederhana',
            'metode_hitung' => $metodeHitung,
        ];
    }

    /**
     * Fallback calculation using simple formula
     */
    private function calculateFallbackPrice(array $data): float
    {
        // Base price per jenis material (Rp/kg atau Rp/unit)
        $materialPrices = [
            'hollow' => 15000,
            'besi_siku' => 12000,
            'aluminium' => 35000,
            'stainless' => 55000,
            'plat' => 18000,
        ];

        // Finishing cost multiplier
        $finishingMultiplier = [
            'cat_biasa' => 1.1,
            'cat_epoxy' => 1.25,
            'powder_coating' => 1.4,
            'galvanis' => 1.3,
        ];

        // Design complexity multiplier
        $complexityMultiplier = [
            1 => 1.0,    // Sederhana
            2 => 1.3,    // Menengah
            3 => 1.7,    // Kompleks
        ];

        $baseMaterialPrice = $materialPrices[$data['jenis_material']] ?? 15000;
        $jumlah_unit = $data['jumlah_unit'];

        // Hitung berdasarkan jenis produk
        if ($data['jenis_produk'] === 'Teralis') {
            // Untuk Teralis: hitung per lubang
            $jumlah_lubang = $data['jumlah_lubang'] ?? 0;
            $hargaPerLubang = 50000; // Base price per lubang
            $basePrice = $jumlah_lubang * $hargaPerLubang * $jumlah_unit;
        } else {
            // Untuk produk lain: hitung per m²
            $ukuran_m2 = $data['ukuran_m2'] ?? 0;
            $hargaPerM2 = $baseMaterialPrice * ($data['ketebalan_mm'] / 10); // Adjust by thickness
            $basePrice = $ukuran_m2 * $hargaPerM2 * $jumlah_unit;
        }

        // Apply finishing multiplier
        $basePrice *= $finishingMultiplier[$data['finishing']] ?? 1.0;

        // Apply complexity multiplier
        $basePrice *= $complexityMultiplier[$data['kerumitan_desain']] ?? 1.0;

        // Add labor cost (40% of base price)
        $totalPrice = $basePrice * 1.4;

        // Round to nearest 1000
        $totalPrice = round($totalPrice / 1000) * 1000;

        return $totalPrice;
    }
}