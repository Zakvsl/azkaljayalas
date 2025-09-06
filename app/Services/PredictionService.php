<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class PredictionService
{
    /**
     * Path to the Python script
     *
     * @var string
     */
    protected $pythonScript;

    /**
     * Python executable path
     *
     * @var string
     */
    protected $pythonExecutable;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pythonScript = base_path('app/Python/Prediction/price_predictor.py');
        
        // Determine Python executable based on OS
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows - assuming Python is in PATH
            $this->pythonExecutable = 'python';
        } else {
            // Linux/Unix/MacOS
            $this->pythonExecutable = 'python3';
        }
    }

    /**
     * Predict price based on input parameters
     *
     * @param array $params
     * @return array
     */
    public function predictPrice(array $params)
    {
        try {
            // Validate required parameters
            $requiredParams = ['product_id', 'material_id', 'finishing_id', 'kerumitan_id', 'ketebalan_id', 'width', 'height', 'quantity'];
            foreach ($requiredParams as $param) {
                if (!isset($params[$param])) {
                    throw new Exception("Missing required parameter: {$param}");
                }
            }

            // Prepare input data for Python script
            $inputJson = json_encode($params);
            
            // Escape the JSON for command line
            $escapedJson = escapeshellarg($inputJson);
            
            // Build the command
            $command = "{$this->pythonExecutable} {$this->pythonScript} {$escapedJson}";
            
            // Execute the command
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            // Check for execution errors
            if ($returnCode !== 0) {
                throw new Exception("Python script execution failed with code {$returnCode}");
            }
            
            // Parse the output
            $result = json_decode($output[0] ?? '{}', true);
            
            // Check for Python script errors
            if (isset($result['error'])) {
                throw new Exception("Python script error: {$result['error']}");
            }
            
            return [
                'success' => true,
                'price' => $result['price'] ?? 0,
                'currency' => $result['currency'] ?? 'IDR',
            ];
            
        } catch (Exception $e) {
            Log::error('Price prediction error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}