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
                    return [
                        'price' => $prediction['predicted_price'],
                        'method' => 'ml',
                        'model_accuracy' => 0.973 // R² score dari metrics.json
                    ];
                } catch (\Exception $e) {
                    Log::warning('ML prediction failed, using fallback calculation', [
                        'error' => $e->getMessage(),
                        'data' => $mlData
                    ]);
                }
            }
            
            // Fallback ke formula sederhana jika ML gagal
            return [
                'price' => $this->calculateFallbackPrice($data),
                'method' => 'fallback',
                'model_accuracy' => null
            ];

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
        // Map jenis_material (keep exact dataset values)
        $materialMap = [
            'Hollow' => 'Hollow',
            'Hollow Stainless' => 'Hollow Stainless',
            'Pipa Stainless' => 'Pipa Stainless',
        ];

        // Map finishing (keep exact dataset values)
        $finishingMap = [
            'Tanpa Cat' => 'Tanpa Cat',
            'Cat Dasar' => 'Cat Dasar',
            'Cat Biasa' => 'Cat Biasa',
            'Cat Duco' => 'Cat Duco',
        ];

        // Support both jenis_produk and produk (backward compat)
        $produk = $data['jenis_produk'] ?? $data['produk'] ?? 'Pagar';
        
        // Determine ukuran_m2 value based on product type
        // Python model always expects Ukuran_m2 field, but value depends on metode_hitung
        if ($produk === 'Railing') {
            // Railing uses ukuran_m, send it as ukuran_m2 for ML model
            $ukuranM2 = (float)($data['ukuran_m'] ?? 0);
            $metodeHitung = 'PER-M';
        } elseif ($produk === 'Teralis') {
            // Teralis uses jumlah_lubang, ukuran_m2 not needed
            $ukuranM2 = 0;
            $metodeHitung = 'PER-LUBANG';
        } else {
            // Others use ukuran_m2 directly
            $ukuranM2 = (float)($data['ukuran_m2'] ?? 0);
            $metodeHitung = 'PER-M2';
        }

        // Calculate upah_tenaga_ahli based on ukuran_m2
        $material = $data['jenis_material'];
        $upahRate = (str_contains($material, 'Stainless')) ? 200000 : 100000;
        $upahTenagaAhli = $upahRate * $ukuranM2;

        return [
            'produk' => $produk,
            'jumlah_unit' => (int)$data['jumlah_unit'],
            'jumlah_lubang' => (float)($data['jumlah_lubang'] ?? 0),
            'ukuran_m2' => $ukuranM2,  // Always use this field for ML
            'jenis_material' => $materialMap[$data['jenis_material']] ?? 'Hollow',
            'ketebalan_mm' => (float)$data['ketebalan_mm'],
            'finishing' => $finishingMap[$data['finishing']] ?? 'Cat Biasa',
            'kerumitan_desain' => $data['kerumitan_desain'] ?? 'Sederhana',
            'profile_size' => $data['profile_size'] ?? '4x4',
            'metode_hitung' => $metodeHitung,
            'upah_tenaga_ahli' => $upahTenagaAhli,
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