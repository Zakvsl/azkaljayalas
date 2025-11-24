<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainingDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trainingData = TrainingData::latest()->paginate(15);
        return view('admin.training-data.index', compact('trainingData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.training-data.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'produk' => 'required|in:Pagar,Kanopi,Railing,Teralis,Pintu',
            'jumlah_unit' => 'required|integer|min:1',
            'jumlah_lubang' => 'nullable|numeric|min:0',
            'ukuran_m2' => 'nullable|numeric|min:0',
            'jenis_material' => 'required|in:Hollow,Besi,Stainless',
            'ketebalan_mm' => 'required|numeric|min:0',
            'finishing' => 'required|in:Cat,Powder Coating,Tanpa Finishing',
            'kerumitan_desain' => 'required|in:Sederhana,Menengah,Kompleks',
            'metode_hitung' => 'required|in:Per m²,Per Lubang',
            'harga_akhir' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        TrainingData::create($validated);

        return redirect()->route('admin.training-data.index')
            ->with('success', 'Data training berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainingData $trainingDatum)
    {
        return view('admin.training-data.show', compact('trainingDatum'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrainingData $trainingDatum)
    {
        return view('admin.training-data.edit', compact('trainingDatum'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrainingData $trainingDatum)
    {
        $validated = $request->validate([
            'produk' => 'required|in:Pagar,Kanopi,Railing,Teralis,Pintu',
            'jumlah_unit' => 'required|integer|min:1',
            'jumlah_lubang' => 'nullable|numeric|min:0',
            'ukuran_m2' => 'nullable|numeric|min:0',
            'jenis_material' => 'required|in:Hollow,Besi,Stainless',
            'ketebalan_mm' => 'required|numeric|min:0',
            'finishing' => 'required|in:Cat,Powder Coating,Tanpa Finishing',
            'kerumitan_desain' => 'required|in:Sederhana,Menengah,Kompleks',
            'metode_hitung' => 'required|in:Per m²,Per Lubang',
            'harga_akhir' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $trainingDatum->update($validated);

        return redirect()->route('admin.training-data.index')
            ->with('success', 'Data training berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrainingData $trainingDatum)
    {
        $trainingDatum->delete();

        return redirect()->route('admin.training-data.index')
            ->with('success', 'Data training berhasil dihapus!');
    }

    /**
     * Delete ALL training data
     */
    public function deleteAll()
    {
        $count = TrainingData::count();
        TrainingData::truncate();

        return redirect()->route('admin.training-data.index')
            ->with('success', "Semua data training ({$count} rows) berhasil dihapus!");
    }

    /**
     * Export training data to CSV for model retraining
     */
    public function export()
    {
        $data = TrainingData::all();
        
        $filename = 'training_data_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
                'Produk', 'Jumlah_Unit', 'Jumlah_Lubang', 'Ukuran_m2',
                'Jenis_Material', 'Ketebalan_mm', 'Finishing',
                'Kerumitan_Desain', 'Metode_Hitung', 'Harga_Akhir'
            ]);

            // Data rows
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->produk,
                    $row->jumlah_unit,
                    $row->jumlah_lubang ?? 0,
                    $row->ukuran_m2 ?? 0,
                    $row->jenis_material,
                    $row->ketebalan_mm,
                    $row->finishing,
                    $row->kerumitan_desain,
                    $row->metode_hitung,
                    $row->harga_akhir
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('admin.training-data.import');
    }

    /**
     * Import training data from CSV/Excel
     * Will DELETE all existing data and replace with new data
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // Max 10MB
            'replace_data' => 'nullable|boolean', // Checkbox to confirm deletion
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            // Read file content
            if ($extension === 'csv') {
                $data = $this->readCsv($file);
            } else {
                // For Excel files, need PhpSpreadsheet library
                return back()->with('error', 'Excel import belum didukung. Gunakan CSV.');
            }

            // DELETE ALL existing data before import (auto-replace)
            $oldCount = TrainingData::count();
            TrainingData::truncate(); // Fast delete all rows

            // Validate and insert data
            $imported = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                try {
                    // Validate row data
                    $validated = $this->validateImportRow($row, $index + 2); // +2 for header row
                    
                    TrainingData::create($validated);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Data lama ({$oldCount} rows) dihapus. Berhasil import {$imported} data baru.";
            if (count($errors) > 0) {
                $message .= " " . count($errors) . " baris gagal.";
            }

            return redirect()->route('admin.training-data.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Read CSV file
     */
    private function readCsv($file)
    {
        $data = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        // Read header
        $header = fgetcsv($handle);
        
        // Normalize header (remove BOM, trim spaces)
        $header = array_map(function($col) {
            return trim(str_replace("\xEF\xBB\xBF", '', $col));
        }, $header);

        // Read data rows
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($header)) {
                $data[] = array_combine($header, $row);
            }
        }

        fclose($handle);
        return $data;
    }

    /**
     * Validate import row data
     * Only extracts required columns, ignores additional columns like ID, timestamps, etc.
     */
    private function validateImportRow(array $row, int $lineNumber): array
    {
        // Map CSV columns to database fields with multiple possible column names
        // Support both underscore and space formats + NEW dataset format (501 rows)
        $columnAlternatives = [
            'produk' => ['Produk', 'produk', 'PRODUK'],
            'jumlah_unit' => ['Jumlah_Unit', 'Jumlah Unit', 'jumlah_unit', 'JUMLAH UNIT'],
            'jumlah_lubang' => ['Jumlah_Lubang', 'Jumlah Lubang', 'jumlah_lubang', 'JUMLAH LUBANG'],
            'ukuran_m2' => ['Ukuran_m2', 'Ukuran (m²)', 'Ukuran_m²', 'ukuran_m2', 'UKURAN (M²)', 'ukuran'], // Added 'ukuran' from new dataset
            'jenis_material' => ['Jenis_Material', 'Jenis Material', 'jenis_material', 'JENIS MATERIAL'],
            'ketebalan_mm' => ['Ketebalan_mm', 'Ketebalan (mm)', 'Ketebalan_mm', 'ketebalan_mm', 'KETEBALAN (MM)', 'ketebalan_material'], // Added 'ketebalan_material' from new dataset
            'finishing' => ['Finishing', 'finishing', 'FINISHING'],
            'kerumitan_desain' => ['Kerumitan_Desain', 'Kerumitan Desain', 'kerumitan_desain', 'KERUMITAN DESAIN'],
            'metode_hitung' => ['Metode_Hitung', 'Metode Hitung', 'metode_hitung', 'METODE HITUNG'],
            'harga_akhir' => ['Harga_Akhir', 'Harga Akhir (Rp)', 'Harga Akhir', 'harga_akhir', 'HARGA AKHIR', 'harga_final'], // Added 'harga_final' from new dataset
        ];

        // Extract only required columns, ignore extra columns
        $data = [];
        $missingColumns = [];
        
        foreach ($columnAlternatives as $dbCol => $possibleNames) {
            $found = false;
            
            // Try each possible column name
            foreach ($possibleNames as $colName) {
                if (isset($row[$colName]) && $row[$colName] !== '' && $row[$colName] !== null) {
                    $data[$dbCol] = $row[$colName];
                    $found = true;
                    break;
                }
                
                // Try case-insensitive match
                foreach (array_keys($row) as $key) {
                    if (strcasecmp($key, $colName) === 0 && $row[$key] !== '' && $row[$key] !== null) {
                        $data[$dbCol] = $row[$key];
                        $found = true;
                        break 2;
                    }
                }
            }
            
            // If not found, check if it's optional (jumlah_lubang or ukuran_m2 can be 0)
            if (!$found) {
                if (in_array($dbCol, ['jumlah_lubang', 'ukuran_m2'])) {
                    $data[$dbCol] = 0; // Set default value
                } else {
                    $missingColumns[] = $possibleNames[0]; // Use first alternative for error message
                }
            }
        }
        
        // Only throw error if required columns are missing
        if (!empty($missingColumns)) {
            throw new \Exception("Kolom yang diperlukan tidak ditemukan: " . implode(', ', $missingColumns));
        }
        
        // Normalize material names - support NEW dataset format (501 rows)
        // IMPORTANT: Database only accepts 3 types: Hollow, Besi, Stainless
        $materialMapping = [
            // Old format
            'Besi Siku' => 'Besi',
            'Plat' => 'Besi',
            'Aluminium' => 'Stainless',
            'Stainless' => 'Stainless',
            'Hollow' => 'Hollow',
            'Besi' => 'Besi',
            // New dataset format (501 rows) - simplify to 3 main types
            'Hollow Stainless' => 'Stainless',
            'Pipa Stainless' => 'Stainless',
        ];
        
        if (isset($data['jenis_material'])) {
            // Direct match first
            if (isset($materialMapping[$data['jenis_material']])) {
                $data['jenis_material'] = $materialMapping[$data['jenis_material']];
            }
            // If not in mapping, try to detect keyword
            elseif (stripos($data['jenis_material'], 'stainless') !== false || stripos($data['jenis_material'], 'stainlis') !== false) {
                $data['jenis_material'] = 'Stainless';
            }
            elseif (stripos($data['jenis_material'], 'hollow') !== false || stripos($data['jenis_material'], 'pipa') !== false) {
                $data['jenis_material'] = 'Hollow';
            }
            elseif (stripos($data['jenis_material'], 'besi') !== false) {
                $data['jenis_material'] = 'Besi';
            }
        }
        
        // Normalize finishing names
        $finishingMapping = [
            'Cat Duco' => 'Cat',
            'Cat Semprot' => 'Cat',
            'Cat' => 'Cat',
            'Cat Biasa' => 'Cat', // New dataset format
            'Cat Dasar' => 'Cat', // New dataset format
            'Powder Coating' => 'Powder Coating',
            'Tanpa Cat' => 'Tanpa Finishing',
            'Tanpa Finishing' => 'Tanpa Finishing',
        ];
        
        if (isset($data['finishing']) && isset($finishingMapping[$data['finishing']])) {
            $data['finishing'] = $finishingMapping[$data['finishing']];
        }
        
        // Normalize metode hitung - NEW dataset uses PER-M2, PER-M, PER-LUBANG
        $metodeMapping = [
            'Per Unit' => 'Per m²',
            'Per m²' => 'Per m²',
            'Per Lubang' => 'Per Lubang',
            'PER-M2' => 'Per m²', // New dataset format
            'PER-M' => 'Per m²', // New dataset format (treat as per m²)
            'PER-LUBANG' => 'Per Lubang', // New dataset format
        ];
        
        if (isset($data['metode_hitung']) && isset($metodeMapping[$data['metode_hitung']])) {
            $data['metode_hitung'] = $metodeMapping[$data['metode_hitung']];
        }
        
        // Normalize produk names - NEW dataset has more specific names
        $produkMapping = [
            'Pintu Gerbang' => 'Pintu',
            'Pintu Handerson' => 'Pintu', // New dataset format
            'Pintu' => 'Pintu',
            'Pagar' => 'Pagar',
            'Kanopi' => 'Kanopi',
            'Teralis' => 'Teralis',
            'Railing' => 'Railing',
        ];
        
        if (isset($data['produk']) && isset($produkMapping[$data['produk']])) {
            $data['produk'] = $produkMapping[$data['produk']];
        }
        
        // Normalize kerumitan desain
        $kerumitanMapping = [
            'Sederhana' => 'Sederhana',
            'Menengah' => 'Menengah',
            'Kompleks' => 'Kompleks',
            'Simple' => 'Sederhana',
            'Medium' => 'Menengah',
            'Complex' => 'Kompleks',
        ];
        
        if (isset($data['kerumitan_desain']) && isset($kerumitanMapping[$data['kerumitan_desain']])) {
            $data['kerumitan_desain'] = $kerumitanMapping[$data['kerumitan_desain']];
        } elseif (!isset($data['kerumitan_desain']) || $data['kerumitan_desain'] === '') {
            $data['kerumitan_desain'] = 'Sederhana'; // Default value
        }
        
        // Clean up numeric values - remove any formatting
        if (isset($data['harga_akhir'])) {
            $data['harga_akhir'] = preg_replace('/[^0-9.]/', '', $data['harga_akhir']);
        }
        
        if (isset($data['ketebalan_mm'])) {
            $data['ketebalan_mm'] = preg_replace('/[^0-9.]/', '', $data['ketebalan_mm']);
        }
        
        if (isset($data['ukuran_m2'])) {
            $data['ukuran_m2'] = preg_replace('/[^0-9.]/', '', $data['ukuran_m2']);
        }
        
        if (isset($data['jumlah_lubang'])) {
            $data['jumlah_lubang'] = preg_replace('/[^0-9.]/', '', $data['jumlah_lubang']);
        }

        // Validate required fields
        $validator = Validator::make($data, [
            'produk' => 'required|in:Pagar,Kanopi,Railing,Teralis,Pintu',
            'jumlah_unit' => 'required|integer|min:1',
            'jumlah_lubang' => 'nullable|numeric|min:0',
            'ukuran_m2' => 'nullable|numeric|min:0',
            'jenis_material' => 'required|in:Hollow,Besi,Stainless',
            'ketebalan_mm' => 'required|numeric|min:0',
            'finishing' => 'required|in:Cat,Powder Coating,Tanpa Finishing',
            'kerumitan_desain' => 'required|in:Sederhana,Menengah,Kompleks',
            'metode_hitung' => 'required|in:Per m²,Per Lubang',
            'harga_akhir' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        return $data;
    }
}
