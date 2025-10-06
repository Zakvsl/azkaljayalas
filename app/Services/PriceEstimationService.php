<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PriceEstimationService
{
    private $pythonPath;
    private $modelPath;

    public function __construct()
    {
        $this->pythonPath = config('app.python_path', 'python');
        $this->modelPath = storage_path('app/models/random_forest_model.joblib');
    }

    public function predictPrice(array $data)
    {
        // Untuk sementara gunakan formula sederhana
        // Nanti akan diganti dengan ML model FastAPI
        try {
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

            $baseMaterialPrice = $materialPrices[$data['jenis_material']];
            $jumlah_unit = $data['jumlah_unit'];

            // Hitung berdasarkan jenis produk
            if ($data['jenis_produk'] === 'Teralis') {
                // Untuk Teralis: hitung per lubang
                $jumlah_lubang = $data['jumlah_lubang'];
                $hargaPerLubang = 50000; // Base price per lubang
                $basePrice = $jumlah_lubang * $hargaPerLubang * $jumlah_unit;
            } else {
                // Untuk produk lain: hitung per m²
                $ukuran_m2 = $data['ukuran_m2'];
                $hargaPerM2 = $baseMaterialPrice * ($data['ketebalan_mm'] / 10); // Adjust by thickness
                $basePrice = $ukuran_m2 * $hargaPerM2 * $jumlah_unit;
            }

            // Apply finishing multiplier
            $basePrice *= $finishingMultiplier[$data['finishing']];

            // Apply complexity multiplier
            $basePrice *= $complexityMultiplier[$data['kerumitan_desain']];

            // Add labor cost (40% of base price)
            $totalPrice = $basePrice * 1.4;

            // Round to nearest 1000
            $totalPrice = round($totalPrice / 1000) * 1000;

            return $totalPrice;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Price prediction failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new \Exception('Gagal menghitung harga: ' . $e->getMessage());
        }
    }

    public function retrainModel()
    {
        $process = new Process([
            $this->pythonPath,
            base_path('python/train.py'),
            $this->modelPath
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return true;
    }
}