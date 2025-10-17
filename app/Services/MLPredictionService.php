<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class MLPredictionService
{
    protected $pythonPath;
    protected $scriptPath;

    public function __construct()
    {
        // Path ke Python executable
        $this->pythonPath = env('PYTHON_PATH', 'python');
        $this->scriptPath = base_path('python/predict_single.py');
    }

    /**
     * Predict price using ML model
     *
     * @param array $data Input data for prediction
     * @return array Prediction result
     * @throws Exception
     */
    public function predict(array $data): array
    {
        // Validate script exists
        if (!file_exists($this->scriptPath)) {
            throw new Exception("Prediction script not found: {$this->scriptPath}");
        }

        // Prepare input data in ML format
        $mlInput = [
            'Produk' => $data['produk'] ?? '',
            'Jumlah_Unit' => (int)($data['jumlah_unit'] ?? 1),
            'Jumlah_Lubang' => (float)($data['jumlah_lubang'] ?? 0),
            'Ukuran_m2' => (float)($data['ukuran_m2'] ?? 0),
            'Jenis_Material' => $data['jenis_material'] ?? '',
            'Ketebalan_mm' => (float)($data['ketebalan_mm'] ?? 0),
            'Finishing' => $data['finishing'] ?? '',
            'Kerumitan_Desain' => $data['kerumitan_desain'] ?? '',
            'Metode_Hitung' => $data['metode_hitung'] ?? '',
        ];

        // Convert to JSON
        $jsonInput = json_encode($mlInput, JSON_UNESCAPED_UNICODE);

        // Execute Python script
        try {
            // Untuk Windows, tulis JSON ke temp file untuk avoid escaping issues
            $tempFile = storage_path('app/temp_ml_input.json');
            file_put_contents($tempFile, $jsonInput);

            $command = sprintf(
                '%s %s %s',
                escapeshellcmd($this->pythonPath),
                escapeshellarg($this->scriptPath),
                escapeshellarg($tempFile)
            );

            exec($command . ' 2>&1', $output, $returnCode);

            $outputString = implode("\n", $output);

            // Clean up temp file
            @unlink($tempFile);

            // Log for debugging
            Log::info('ML Prediction', [
                'input' => $mlInput,
                'json' => $jsonInput,
                'command' => $command,
                'output' => $outputString,
                'return_code' => $returnCode
            ]);

            if ($returnCode !== 0) {
                throw new Exception("Python script failed with code {$returnCode}: {$outputString}");
            }

            // Parse JSON output
            $result = json_decode($outputString, true);

            if (!$result || !isset($result['success'])) {
                throw new Exception("Invalid response from prediction script: {$outputString}");
            }

            if (!$result['success']) {
                throw new Exception($result['message'] ?? 'Prediction failed');
            }

            return $result;

        } catch (Exception $e) {
            Log::error('ML Prediction Error', [
                'error' => $e->getMessage(),
                'input' => $mlInput
            ]);
            throw $e;
        }
    }

    /**
     * Train model with new dataset
     *
     * @param string $datasetPath Path to CSV dataset
     * @return array Training results
     * @throws Exception
     */
    public function trainModel(string $datasetPath): array
    {
        if (!file_exists($datasetPath)) {
            throw new Exception("Dataset file not found: {$datasetPath}");
        }

        $trainScript = base_path('python/train_model.py');
        
        if (!file_exists($trainScript)) {
            throw new Exception("Training script not found: {$trainScript}");
        }

        try {
            $command = sprintf(
                '%s %s %s',
                escapeshellcmd($this->pythonPath),
                escapeshellarg($trainScript),
                escapeshellarg($datasetPath)
            );

            exec($command . ' 2>&1', $output, $returnCode);

            $outputString = implode("\n", $output);

            Log::info('ML Training', [
                'command' => $command,
                'output' => $outputString,
                'return_code' => $returnCode
            ]);

            if ($returnCode !== 0) {
                throw new Exception("Training failed with code {$returnCode}: {$outputString}");
            }

            // Check if metrics file was created
            $metricsPath = base_path('python/metrics.json');
            if (file_exists($metricsPath)) {
                $metricsRaw = json_decode(file_get_contents($metricsPath), true);
                
                // Normalize metric keys (Python uses test_mae, test_rmse, test_r2)
                $metrics = [
                    'mae' => $metricsRaw['test_mae'] ?? $metricsRaw['mae'] ?? 0,
                    'rmse' => $metricsRaw['test_rmse'] ?? $metricsRaw['rmse'] ?? 0,
                    'r2' => $metricsRaw['test_r2'] ?? $metricsRaw['r2'] ?? 0,
                    'cv_mae' => $metricsRaw['cv_mae'] ?? null,
                    'cv_r2' => $metricsRaw['cv_r2'] ?? null,
                ];
                
                Log::info('Metrics loaded', [
                    'raw' => $metricsRaw,
                    'normalized' => $metrics
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Model trained successfully',
                    'metrics' => $metrics,
                    'output' => $outputString
                ];
            }

            return [
                'success' => true,
                'message' => 'Model trained successfully',
                'output' => $outputString
            ];

        } catch (Exception $e) {
            Log::error('ML Training Error', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if model is trained and ready
     *
     * @return bool
     */
    public function isModelTrained(): bool
    {
        $modelPath = base_path('python/model_pipeline.joblib');
        return file_exists($modelPath);
    }

    /**
     * Get model metrics and information
     *
     * @return array
     */
    public function getModelInfo(): array
    {
        $modelPath = base_path('python/model_pipeline.joblib');
        $metricsPath = base_path('python/metrics.json');
        $importancesPath = base_path('python/feature_importances.json');

        if (!file_exists($modelPath)) {
            return [
                'trained' => false,
                'message' => 'Model belum ditraining'
            ];
        }

        $info = [
            'trained' => true,
            'model_path' => $modelPath,
            'last_modified' => date('Y-m-d H:i:s', filemtime($modelPath))
        ];

        // Load metrics if available
        if (file_exists($metricsPath)) {
            $metricsRaw = json_decode(file_get_contents($metricsPath), true);
            
            // Normalize metric keys (Python uses test_mae, test_rmse, test_r2)
            $info['metrics'] = [
                'mae' => $metricsRaw['test_mae'] ?? $metricsRaw['mae'] ?? 0,
                'rmse' => $metricsRaw['test_rmse'] ?? $metricsRaw['rmse'] ?? 0,
                'r2' => $metricsRaw['test_r2'] ?? $metricsRaw['r2'] ?? 0,
                'cv_mae' => $metricsRaw['cv_mae'] ?? null,
                'cv_r2' => $metricsRaw['cv_r2'] ?? null,
            ];
        }

        // Load feature importances if available
        if (file_exists($importancesPath)) {
            $info['feature_importances'] = json_decode(file_get_contents($importancesPath), true);
        }

        return $info;
    }
}
