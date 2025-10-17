<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as BaseController;
use App\Models\TrainingData;
use App\Services\MLPredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class MLModelController extends BaseController
{
    protected $mlService;

    public function __construct(MLPredictionService $mlService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mlService = $mlService;
    }

    /**
     * Display ML model dashboard
     */
    public function index()
    {
        $modelInfo = $this->mlService->getModelInfo();
        $trainingDataCount = TrainingData::count();
        
        return view('admin.ml.index', compact('modelInfo', 'trainingDataCount'));
    }

    /**
     * Retrain the ML model using training data from database
     */
    public function retrain(Request $request)
    {
        try {
            Log::info('Starting model retraining process');

            // Check if we have training data
            $trainingDataCount = TrainingData::count();
            Log::info('Training data count: ' . $trainingDataCount);
            
            if ($trainingDataCount < 10) {
                return back()->with('error', 'Minimal 10 data training diperlukan untuk melatih model. Saat ini ada ' . $trainingDataCount . ' data.');
            }

            // Export training data to temporary CSV
            $tempCsvPath = storage_path('app/temp_training_data.csv');
            $this->exportTrainingDataToCsv($tempCsvPath);

            // Verify CSV was created successfully
            if (!file_exists($tempCsvPath)) {
                throw new \Exception('Failed to create temporary CSV file');
            }
            
            $csvSize = filesize($tempCsvPath);
            Log::info('Training data exported', [
                'path' => $tempCsvPath,
                'size' => $csvSize,
                'exists' => file_exists($tempCsvPath)
            ]);
            
            if ($csvSize < 100) {
                throw new \Exception('CSV file is too small, possibly empty');
            }

            // Call MLPredictionService to train model
            $result = $this->mlService->trainModel($tempCsvPath);

            // Keep temp file for debugging if training failed
            if ($result['success'] && file_exists($tempCsvPath)) {
                unlink($tempCsvPath);
                Log::info('Temporary CSV file deleted');
            } else {
                Log::warning('Keeping temp CSV file for debugging at: ' . $tempCsvPath);
            }

            if ($result['success']) {
                Log::info('Model retrained successfully', $result);
                
                $message = 'Model berhasil dilatih ulang dengan ' . $trainingDataCount . ' data! ';
                if (isset($result['metrics'])) {
                    $metrics = $result['metrics'];
                    
                    // Check if metrics look suspicious (all zeros)
                    if ($metrics['mae'] == 0 && $metrics['rmse'] == 0 && $metrics['r2'] == 0) {
                        Log::warning('Suspicious metrics detected - all zeros!', $metrics);
                        $message .= 'WARNING: Metrics menunjukkan nilai 0 - mungkin ada masalah dengan data atau training process. ';
                    }
                    
                    $message .= sprintf(
                        'MAE: %s, RMSE: %s, R²: %.4f',
                        number_format($metrics['mae'] ?? 0, 0, ',', '.'),
                        number_format($metrics['rmse'] ?? 0, 0, ',', '.'),
                        $metrics['r2'] ?? 0
                    );
                }
                
                return back()->with('success', $message);
            } else {
                Log::error('Model retraining failed', $result);
                return back()->with('error', 'Gagal melatih model: ' . ($result['error'] ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            Log::error('Exception during model retraining: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export training data to CSV file
     * IMPORTANT: Column names must match Python script expectations (with spaces, not underscores)
     */
    protected function exportTrainingDataToCsv($path)
    {
        $trainingData = TrainingData::all();

        Log::info('Exporting training data to CSV', ['count' => $trainingData->count()]);

        $file = fopen($path, 'w');
        
        // Header row - MUST match Python train_model.py expectations (with spaces!)
        fputcsv($file, [
            'Produk',
            'Jumlah Unit',        // Space, not underscore!
            'Jumlah Lubang',      // Space, not underscore!
            'Ukuran_m2',          // Keep underscore for this one
            'Jenis Material',     // Space, not underscore!
            'Ketebalan_mm',       // Keep underscore
            'Finishing',
            'Kerumitan Desain',   // Space, not underscore!
            'Metode Hitung',      // Space, not underscore!
            'Harga_Akhir_Rp'      // Match Python target column name
        ]);

        // Data rows
        $rowCount = 0;
        foreach ($trainingData as $data) {
            fputcsv($file, [
                $data->produk,
                $data->jumlah_unit,
                $data->jumlah_lubang ?? 0,
                $data->ukuran_m2 ?? 0,
                $data->jenis_material,
                $data->ketebalan_mm,
                $data->finishing,
                $data->kerumitan_desain,
                $data->metode_hitung,
                $data->harga_akhir
            ]);
            $rowCount++;
        }

        fclose($file);
        
        Log::info('CSV export completed', [
            'path' => $path,
            'rows' => $rowCount,
            'file_size' => filesize($path)
        ]);
    }

    /**
     * Download model metrics as JSON
     */
    public function downloadMetrics()
    {
        $metricsPath = base_path('python/metrics.json');
        
        if (!file_exists($metricsPath)) {
            return back()->with('error', 'File metrics tidak ditemukan. Silakan latih model terlebih dahulu.');
        }

        return response()->download($metricsPath, 'model_metrics.json');
    }

    /**
     * Download feature importances as JSON
     */
    public function downloadFeatureImportances()
    {
        $featurePath = base_path('python/feature_importances.json');
        
        if (!file_exists($featurePath)) {
            return back()->with('error', 'File feature importances tidak ditemukan. Silakan latih model terlebih dahulu.');
        }

        return response()->download($featurePath, 'feature_importances.json');
    }

    /**
     * Train the ML model (legacy method for backward compatibility)
     */
    public function train(Request $request)
    {
        // Redirect to retrain method which now uses training_data table
        return $this->retrain($request);
    }

    /**
     * Test prediction (currently disabled - use estimates.store instead)
     */
    public function testPrediction(Request $request)
    {
        return response()->json([
            'success' => false,
            'error' => 'This endpoint is deprecated. Please use the estimates form instead.'
        ], 400);
        
        /*
        $validated = $request->validate([
            'jenis_proyek' => 'required|string',
            'panjang' => 'required|numeric|min:0',
            'lebar' => 'required|numeric|min:0',
            'tinggi' => 'required|numeric|min:0',
            'material' => 'required|string',
            'kompleksitas' => 'required|string',
        ]);

        try {
            $prediction = $this->mlService->predictPrice($validated);

            return response()->json([
                'success' => true,
                'predicted_price' => $prediction,
                'formatted_price' => 'Rp ' . number_format($prediction, 0, ',', '.')
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
        */
    }
}
