<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class MLPredictionService
{
    protected $pythonPath;
    protected $scriptPath;

    public function __construct()
    {
        // Path ke Python executable (adjust sesuai environment Anda)
        $this->pythonPath = env('PYTHON_PATH', 'python');
        $this->scriptPath = base_path('ml/price_prediction.py');
    }

    /**
     * Train the ML model with dataset
     *
     * @param string $datasetPath Path to the dataset file
     * @return array Training results
     * @throws Exception
     */
    public function trainModel(string $datasetPath): array
    {
        if (!file_exists($datasetPath)) {
            throw new Exception("Dataset file not found: {$datasetPath}");
        }

        $command = sprintf(
            '%s %s train %s 2>&1',
            escapeshellcmd($this->pythonPath),
            escapeshellarg($this->scriptPath),
            escapeshellarg($datasetPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $errorMessage = implode("\n", $output);
            Log::error('ML Training failed', ['error' => $errorMessage]);
            throw new Exception("Training failed: {$errorMessage}");
        }

        $result = implode("\n", $output);
        return json_decode($result, true) ?? [];
    }

    /**
     * Predict price based on input features
     *
     * @param array $features Input features for prediction
     * @return float Predicted price
     * @throws Exception
     */
    public function predictPrice(array $features): float
    {
        $inputJson = json_encode($features);

        $command = sprintf(
            '%s %s predict %s 2>&1',
            escapeshellcmd($this->pythonPath),
            escapeshellarg($this->scriptPath),
            escapeshellarg($inputJson)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $errorMessage = implode("\n", $output);
            Log::error('ML Prediction failed', ['error' => $errorMessage]);
            throw new Exception("Prediction failed: {$errorMessage}");
        }

        $result = implode("\n", $output);
        $data = json_decode($result, true);

        if (!isset($data['predicted_price'])) {
            throw new Exception("Invalid prediction response");
        }

        return (float) $data['predicted_price'];
    }

    /**
     * Check if model is trained and ready
     *
     * @return bool
     */
    public function isModelTrained(): bool
    {
        $modelPath = base_path('ml/models/price_model.pkl');
        return file_exists($modelPath);
    }

    /**
     * Get model metrics and information
     *
     * @return array
     */
    public function getModelInfo(): array
    {
        if (!$this->isModelTrained()) {
            return [
                'trained' => false,
                'message' => 'Model belum ditraining'
            ];
        }

        return [
            'trained' => true,
            'model_path' => base_path('ml/models/price_model.pkl'),
            'last_modified' => date('Y-m-d H:i:s', filemtime(base_path('ml/models/price_model.pkl')))
        ];
    }
}
