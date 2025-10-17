<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test data
$testData = [
    'jenis_produk' => 'Pagar',
    'jumlah_unit' => 1,
    'jumlah_lubang' => 0,
    'ukuran_m2' => 10,
    'jenis_material' => 'hollow',
    'profile_size' => '40x40',
    'ketebalan_mm' => 2,
    'finishing' => 'cat_biasa',
    'kerumitan_desain' => 1
];

echo "Testing Estimasi Endpoint\n";
echo "=========================\n\n";

echo "Test Data:\n";
print_r($testData);
echo "\n";

// Create request
$request = Illuminate\Http\Request::create(
    '/estimates/calculate',
    'POST',
    $testData,
    [],
    [],
    ['CONTENT_TYPE' => 'application/json'],
    json_encode($testData)
);

try {
    $response = $kernel->handle($request);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content:\n";
    echo $response->getContent() . "\n\n";
    
    $data = json_decode($response->getContent(), true);
    
    if (isset($data['success']) && $data['success']) {
        echo "✅ SUCCESS!\n";
        echo "Harga: " . ($data['formatted_price'] ?? 'N/A') . "\n";
    } else {
        echo "❌ FAILED!\n";
        echo "Message: " . ($data['message'] ?? 'Unknown error') . "\n";
        if (isset($data['errors'])) {
            echo "Errors:\n";
            print_r($data['errors']);
        }
    }
    
} catch (\Exception $e) {
    echo "❌ EXCEPTION!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}
