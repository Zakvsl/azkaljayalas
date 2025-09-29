<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MLController extends Controller
{
    protected $pythonPath;
    protected $modelPath;
    protected $trainingDataPath;
    protected $modelHistoryPath;

    public function __construct()
    {
        $this->pythonPath = base_path('python');
        $this->modelPath = storage_path('app/models/price_model.joblib');
        $this->trainingDataPath = storage_path('app/data/training_data.csv');
        $this->modelHistoryPath = storage_path('app/models/history');
    }

    /**
     * Get the current status of the ML model
     */
    public function getModelStatus()
    {
        try {
            $modelExists = file_exists($this->modelPath);
            $lastTraining = $modelExists ? Carbon::createFromTimestamp(filemtime($this->modelPath))->diffForHumans() : 'Never';
            
            // Get the latest version from history
            $history = collect(Storage::files('models/history'))
                ->map(function ($file) {
                    return json_decode(Storage::get($file), true);
                })
                ->sortByDesc('created_at')
                ->values()
                ->all();
            $currentVersion = count($history) > 0 ? $history[0]['version'] : 'N/A';
            
            // Get training data size
            $dataSize = file_exists($this->trainingDataPath) ? 
                count(file($this->trainingDataPath)) - 1 . ' samples' : // Subtract header row
                'No data';

            return response()->json([
                'status' => 'success',
                'last_training' => $lastTraining,
                'version' => $currentVersion,
                'data_size' => $dataSize,
                'model_exists' => $modelExists
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting model status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get model status'
            ], 500);
        }
    }

    /**
     * Get the training history of the model
     */
    public function getTrainingHistory()
    {
        try {
            if (!Storage::exists('models/history')) {
                return response()->json([
                    'status' => 'success',
                    'history' => []
                ]);
            }

            $history = collect(Storage::files('models/history'))
                ->map(function ($file) {
                    $content = json_decode(Storage::get($file), true);
                    $content['created_at'] = Carbon::createFromTimestamp(Storage::lastModified($file))->toDateTimeString();
                    return $content;
                })
                ->sortByDesc('created_at')
                ->values()
                ->all();

            return response()->json([
                'status' => 'success',
                'history' => $history
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting training history: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get training history'
            ], 500);
        }
    }

    /**
     * Rollback to a previous model version
     */
    public function rollbackModel($version)
    {
        try {
            $historyFile = "models/history/{$version}.json";
            
            if (!Storage::exists($historyFile)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Model version not found'
                ], 404);
            }

            $modelFile = "models/{$version}.joblib";
            if (!Storage::exists($modelFile)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Model file not found'
                ], 404);
            }

            // Copy the old model to current
            Storage::copy($modelFile, 'models/price_model.joblib');

            // Get the metrics from history
            $metrics = json_decode(Storage::get($historyFile), true);

            return response()->json([
                'status' => 'success',
                'message' => "Successfully rolled back to version {$version}",
                'metrics' => $metrics
            ]);

        } catch (\Exception $e) {
            Log::error('Error rolling back model: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to rollback model'
            ], 500);
        }
    }

    /**
     * Retrain the price estimation model with current data
     */
    public function retrainModel()
    {
        try {
            // Ensure storage directories exist
            Storage::makeDirectory('models');
            Storage::makeDirectory('models/history');
            Storage::makeDirectory('data');

            // Generate version
            $version = date('Ymd_His') . '_' . Str::random(8);
            $versionedModelPath = storage_path("app/models/{$version}.joblib");

            // Generate fresh training data
            $result = Process::path($this->pythonPath)
                ->run('python generate_data.py');

            if ($result->failed()) {
                Log::error('Failed to generate training data: ' . $result->errorOutput());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to generate training data',
                    'error' => $result->errorOutput()
                ], 500);
            }

            // Train the model
            $result = Process::path($this->pythonPath)
                ->run('python train.py ' . $versionedModelPath);

            if ($result->failed()) {
                Log::error('Failed to train model: ' . $result->errorOutput());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to train model',
                    'error' => $result->errorOutput()
                ], 500);
            }

            // Parse training metrics
            $output = $result->output();
            $metrics = json_decode($output, true);

            if ($metrics['status'] === 'success') {
                // Save version info
                $versionInfo = [
                    'version' => $version,
                    'metrics' => $metrics['metrics'],
                    'created_at' => now()->toDateTimeString()
                ];
                
                Storage::put(
                    "models/history/{$version}.json",
                    json_encode($versionInfo, JSON_PRETTY_PRINT)
                );

                // Copy to current model
                copy($versionedModelPath, $this->modelPath);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Model retrained successfully',
                    'metrics' => $metrics['metrics'],
                    'version' => $version
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Training failed to produce valid metrics',
                'error' => $metrics['message'] ?? 'Unknown error'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error in model retraining: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a price estimate using the trained model
     */
    public function getPriceEstimate(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'project_type' => 'required|string',
                'material_type' => 'required|string',
                'dimensions' => 'required|array',
                'dimensions.length' => 'required|numeric|min:0',
                'dimensions.width' => 'required|numeric|min:0',
                'dimensions.thickness' => 'required|numeric|min:0',
                'additional_features' => 'array'
            ]);

            // Ensure model exists
            if (!file_exists($this->modelPath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Model not found. Please train the model first.'
                ], 404);
            }

            // Prepare input data for prediction
            $inputJson = json_encode($validatedData);

            // Run prediction
            $result = Process::path($this->pythonPath)
                ->run('python predict.py ' . escapeshellarg($inputJson) . ' ' . $this->modelPath);

            if ($result->failed()) {
                Log::error('Prediction failed: ' . $result->errorOutput());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to get price estimate',
                    'error' => $result->errorOutput()
                ], 500);
            }

            // Parse prediction result
            $prediction = json_decode($result->output(), true);

            return response()->json([
                'status' => 'success',
                'data' => $prediction
            ]);

        } catch (\Exception $e) {
            Log::error('Error in price estimation: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}