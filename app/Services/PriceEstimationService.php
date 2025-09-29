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
        try {
            $process = new Process([
                $this->pythonPath,
                base_path('python/predict.py'),
                json_encode([
                    'project_type' => $data['project_type'],
                    'material_type' => $data['material_type'],
                    'dimensions' => [
                        'length' => (float) $data['dimensions']['length'],
                        'width' => (float) $data['dimensions']['width'],
                        'thickness' => (float) $data['dimensions']['thickness'],
                    ],
                    'additional_features' => $data['additional_features'] ?? [],
                ]),
                $this->modelPath
            ]);

            $process->setTimeout(30);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $result = json_decode($process->getOutput(), true);
            return floatval($result['predicted_price']);
        } catch (\Exception $e) {
            throw new \Exception('Failed to generate price prediction: ' . $e->getMessage());
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