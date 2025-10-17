<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Admin\MLModelController;
use App\Models\TrainingData;

echo "=== DEBUGGING ML TRAINING ISSUE ===\n\n";

// 1. Check training data count
$count = TrainingData::count();
echo "1. Training data count: $count\n\n";

// 2. Check sample data
$sample = TrainingData::first();
if ($sample) {
    echo "2. Sample data:\n";
    echo "   Produk: {$sample->produk}\n";
    echo "   Jumlah Unit: {$sample->jumlah_unit}\n";
    echo "   Jenis Material: {$sample->jenis_material}\n";
    echo "   Harga Akhir: {$sample->harga_akhir}\n\n";
}

// 3. Test CSV export
echo "3. Testing CSV export...\n";
$controller = new MLModelController(app(\App\Services\MLPredictionService::class));
$tempPath = storage_path('app/debug_training.csv');

// Use reflection to call protected method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('exportTrainingDataToCsv');
$method->setAccessible(true);
$method->invoke($controller, $tempPath);

if (file_exists($tempPath)) {
    $size = filesize($tempPath);
    echo "   CSV created: $tempPath\n";
    echo "   File size: $size bytes\n\n";
    
    // Read first few lines
    echo "4. CSV Content (first 5 lines):\n";
    $handle = fopen($tempPath, 'r');
    for ($i = 0; $i < 5 && ($line = fgets($handle)) !== false; $i++) {
        echo "   Line $i: " . trim($line) . "\n";
    }
    fclose($handle);
    
    echo "\n5. Recommendation:\n";
    echo "   ✓ CSV file exists and has content\n";
    echo "   ✓ Check if column names match Python script expectations\n";
    echo "   ✓ Python script expects: 'Jumlah Unit' (with space)\n";
    echo "   ✓ Check logs at: storage/logs/laravel.log\n";
} else {
    echo "   ERROR: CSV file was not created!\n";
}

echo "\nDone!\n";
