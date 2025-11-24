<?php

namespace App\Services;

/**
 * Service untuk auto-calculate atribut teknis dan biaya karyawan
 * Digunakan untuk memperkaya dataset ML dengan atribut turunan yang realistis
 */
class EstimationCalculator
{
    /**
     * Hitung semua atribut turunan secara otomatis
     * 
     * @param array $data Data input dari form
     * @return array Atribut turunan yang dihitung
     */
    public function calculateDerivedAttributes(array $data): array
    {
        $produk = $data['produk'] ?? $data['jenis_produk'] ?? '';
        $ukuran_m2 = (float)($data['ukuran_m2'] ?? 0);
        $jumlah_lubang = (int)($data['jumlah_lubang'] ?? 0);
        $kerumitan = $data['kerumitan_desain'] ?? 'Sederhana';
        $finishing = $data['finishing'] ?? 'cat_biasa';
        $metode_hitung = $data['metode_hitung'] ?? 'Per m²';
        $jenis_material = $data['jenis_material'] ?? 'hollow';
        $ketebalan_mm = (float)($data['ketebalan_mm'] ?? 1.0);
        
        // 1. Hitung Jumlah Sambungan Las
        $jumlah_sambungan = $this->calculateJumlahSambungan(
            $produk, $ukuran_m2, $jumlah_lubang, $kerumitan, $metode_hitung
        );
        
        // 2. Hitung Jumlah Potongan
        $jumlah_potongan = $this->calculateJumlahPotongan($jumlah_sambungan, $kerumitan);
        
        // 3. Hitung Volume Material
        $volume_material = $this->calculateVolumeMaterial($ukuran_m2, $ketebalan_mm);
        
        // 4. Hitung Complexity Score (1-10)
        $complexity_score = $this->calculateComplexityScore(
            $kerumitan, $finishing, $data['tingkat_kesulitan_akses'] ?? 'Mudah'
        );
        
        // 5. Hitung Estimasi Hari Kerja
        $estimasi_hari_kerja = $this->calculateEstimasiHariKerja(
            $ukuran_m2, $jumlah_lubang, $kerumitan, $produk, $finishing, $metode_hitung
        );
        
        // 6. Tentukan Jumlah Tukang yang Dibutuhkan
        $jumlah_tukang = $this->calculateJumlahTukang(
            $ukuran_m2, $jumlah_lubang, $kerumitan, $estimasi_hari_kerja
        );
        
        // 7. Tentukan Biaya Tukang Per Hari (berdasarkan skill/kerumitan)
        $biaya_tukang_per_hari = $this->calculateBiayaTukangPerHari($kerumitan, $jenis_material);
        
        // 8. Hitung Total Biaya Karyawan
        $total_biaya_karyawan = $jumlah_tukang * $estimasi_hari_kerja * $biaya_tukang_per_hari;
        
        return [
            'jumlah_sambungan' => $jumlah_sambungan,
            'jumlah_potongan' => $jumlah_potongan,
            'volume_material' => round($volume_material, 4),
            'complexity_score' => $complexity_score,
            'estimasi_hari_kerja' => $estimasi_hari_kerja,
            'jumlah_tukang' => $jumlah_tukang,
            'biaya_tukang_per_hari' => $biaya_tukang_per_hari,
            'total_biaya_karyawan' => $total_biaya_karyawan,
        ];
    }
    
    /**
     * Hitung jumlah sambungan las berdasarkan ukuran dan kompleksitas
     */
    protected function calculateJumlahSambungan(
        string $produk, 
        float $ukuran_m2, 
        int $jumlah_lubang, 
        string $kerumitan,
        string $metode_hitung
    ): int {
        $sambungan = 0;
        
        if ($metode_hitung === 'Per Lubang' || $produk === 'Teralis') {
            // Teralis: 4 sambungan per lubang + frame
            $sambungan = $jumlah_lubang * 4 + 20; // 20 untuk frame
        } else {
            // Produk lain: 8-12 sambungan per m²
            $base_per_m2 = match($produk) {
                'Pagar' => 8,
                'Kanopi' => 6,
                'Railing' => 10,
                'Pintu' => 12,
                default => 8
            };
            $sambungan = (int)ceil($ukuran_m2 * $base_per_m2);
        }
        
        // Tambahan dari kerumitan
        $sambungan += match($kerumitan) {
            'Kompleks' => 25,
            'Menengah' => 15,
            default => 0
        };
        
        return max(10, $sambungan); // Minimal 10 sambungan
    }
    
    /**
     * Hitung jumlah potongan material
     */
    protected function calculateJumlahPotongan(int $jumlah_sambungan, string $kerumitan): int
    {
        // Rata-rata 2.5 sambungan per potongan
        $base = (int)ceil($jumlah_sambungan / 2.5);
        
        // Tambahan untuk kerumitan
        $tambahan = match($kerumitan) {
            'Kompleks' => 10,
            'Menengah' => 5,
            default => 0
        };
        
        return max(5, $base + $tambahan);
    }
    
    /**
     * Hitung volume material estimasi (m³)
     */
    protected function calculateVolumeMaterial(float $ukuran_m2, float $ketebalan_mm): float
    {
        // Volume = luas × ketebalan (konversi mm ke m)
        return $ukuran_m2 * ($ketebalan_mm / 1000);
    }
    
    /**
     * Hitung complexity score (1-10)
     */
    protected function calculateComplexityScore(
        string $kerumitan, 
        string $finishing, 
        string $akses
    ): int {
        $score = 0;
        
        // Dari kerumitan desain (0-4)
        $score += match($kerumitan) {
            'Kompleks' => 4,
            'Menengah' => 2,
            default => 0
        };
        
        // Dari finishing (0-3)
        $score += match($finishing) {
            'powder_coating' => 3,
            'cat_epoxy' => 2,
            'cat_biasa' => 1,
            default => 0
        };
        
        // Dari kesulitan akses (0-3)
        $score += match($akses) {
            'Sulit' => 3,
            'Sedang' => 1,
            default => 0
        };
        
        return min(10, max(1, $score));
    }
    
    /**
     * Hitung estimasi hari kerja
     * Formula: Base days dari ukuran + faktor kompleksitas + faktor finishing
     */
    protected function calculateEstimasiHariKerja(
        float $ukuran_m2,
        int $jumlah_lubang,
        string $kerumitan,
        string $produk,
        string $finishing,
        string $metode_hitung
    ): int {
        $hari = 0;
        
        // Base days dari ukuran
        if ($metode_hitung === 'Per Lubang' || $produk === 'Teralis') {
            // Teralis: 1 hari per 10 lubang
            $hari = max(1, (int)ceil($jumlah_lubang / 10));
        } else {
            // Produk lain: 1 hari per 8-12 m² tergantung produk
            $m2_per_hari = match($produk) {
                'Kanopi' => 12, // Lebih cepat (struktur sederhana)
                'Pagar' => 10,
                'Railing' => 8,  // Lebih lama (detail banyak)
                'Pintu' => 6,    // Paling lama (precision tinggi)
                default => 10
            };
            $hari = max(1, (int)ceil($ukuran_m2 / $m2_per_hari));
        }
        
        // Tambahan dari kerumitan
        $hari += match($kerumitan) {
            'Kompleks' => 4,
            'Menengah' => 2,
            default => 0
        };
        
        // Tambahan dari finishing
        $hari += match($finishing) {
            'powder_coating' => 3, // Perlu ke tempat powder coating
            'cat_epoxy' => 2,
            'cat_biasa' => 1,
            default => 0
        };
        
        return min(30, max(1, $hari)); // Range: 1-30 hari
    }
    
    /**
     * Tentukan jumlah tukang yang dibutuhkan
     * Formula: Berdasarkan ukuran proyek dan timeline
     */
    protected function calculateJumlahTukang(
        float $ukuran_m2,
        int $jumlah_lubang,
        string $kerumitan,
        int $estimasi_hari_kerja
    ): int {
        // Kalau proyek besar tapi deadline pendek, butuh lebih banyak tukang
        $ukuran = max($ukuran_m2, $jumlah_lubang);
        
        if ($ukuran > 30) {
            $tukang = 4; // Proyek besar
        } elseif ($ukuran > 15) {
            $tukang = 3; // Proyek menengah
        } elseif ($ukuran > 8) {
            $tukang = 2; // Proyek kecil-menengah
        } else {
            $tukang = 1; // Proyek kecil
        }
        
        // Tambahan untuk kerumitan tinggi
        if ($kerumitan === 'Kompleks' && $tukang < 3) {
            $tukang++;
        }
        
        return min(5, max(1, $tukang)); // Range: 1-5 tukang
    }
    
    /**
     * Tentukan biaya tukang per hari berdasarkan skill yang dibutuhkan
     * Formula: Tukang biasa vs skilled vs expert
     */
    protected function calculateBiayaTukangPerHari(string $kerumitan, string $jenis_material): int
    {
        // Base rate berdasarkan kerumitan (menentukan skill level)
        $base_rate = match($kerumitan) {
            'Kompleks' => 250000,  // Expert welder
            'Menengah' => 200000,  // Skilled welder
            default => 150000      // Standard welder
        };
        
        // Tambahan untuk material khusus
        $material_bonus = match($jenis_material) {
            'stainless' => 30000,   // Stainless butuh skill khusus
            'aluminium' => 20000,   // Aluminium butuh setting khusus
            default => 0
        };
        
        return $base_rate + $material_bonus;
    }
}
