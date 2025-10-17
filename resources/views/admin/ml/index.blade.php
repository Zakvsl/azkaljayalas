@extends('layouts.admin')

@section('title', 'ML Model Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-brain mr-2"></i>Machine Learning Model Management
        </h1>
        <p class="mt-2 text-sm text-gray-600">Kelola dan latih model prediksi harga menggunakan data training</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Model Status Card -->
        <div class="bg-white shadow-md rounded-lg p-6 border-t-4 {{ $modelInfo['trained'] ? 'border-green-500' : 'border-yellow-500' }}">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Status Model</h3>
                @if($modelInfo['trained'])
                    <span class="flex items-center text-green-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </span>
                @else
                    <span class="flex items-center text-yellow-600">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </span>
                @endif
            </div>
            
            @if($modelInfo['trained'])
                <p class="text-sm text-gray-600 mb-2">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <strong>Last Modified:</strong><br>
                    <span class="ml-6">{{ $modelInfo['last_modified'] }}</span>
                </p>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-file mr-2"></i>
                    <strong>Model File:</strong><br>
                    <span class="ml-6">{{ basename($modelInfo['model_path']) }}</span>
                </p>
            @else
                <p class="text-sm text-yellow-700">Model belum pernah dilatih. Silakan latih model menggunakan data training.</p>
            @endif
        </div>

        <!-- Training Data Info -->
        <div class="bg-white shadow-md rounded-lg p-6 border-t-4 border-blue-500">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Data Training</h3>
                <span class="flex items-center text-blue-600">
                    <i class="fas fa-database text-2xl"></i>
                </span>
            </div>
            
            <p class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($trainingDataCount) }}</p>
            <p class="text-sm text-gray-600 mb-4">Total data tersedia</p>
            
            @if($trainingDataCount < 10)
                <p class="text-xs text-red-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Minimal 10 data diperlukan untuk training
                </p>
            @else
                <p class="text-xs text-green-600">
                    <i class="fas fa-check mr-1"></i>
                    Data sudah cukup untuk training
                </p>
            @endif
            
            <a href="{{ route('admin.training-data.index') }}" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-right mr-1"></i>
                Kelola Data Training
            </a>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow-md rounded-lg p-6 border-t-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-bolt mr-2"></i>Quick Actions
            </h3>
            
            <div class="space-y-3">
                <form action="{{ route('admin.ml.retrain') }}" method="POST" onsubmit="return confirm('Training model akan menggunakan semua data training yang ada. Proses ini memakan waktu 1-3 menit. Lanjutkan?')">
                    @csrf
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition duration-200 flex items-center justify-center"
                            {{ $trainingDataCount < 10 ? 'disabled' : '' }}>
                        <i class="fas fa-sync-alt mr-2"></i>
                        {{ $modelInfo['trained'] ? 'Retrain Model' : 'Train Model' }}
                    </button>
                </form>
                
                <a href="{{ route('admin.training-data.create') }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 text-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Data
                </a>
                
                <a href="{{ route('admin.training-data.import-form') }}" class="block w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200 text-center">
                    <i class="fas fa-file-upload mr-2"></i>
                    Import CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Model Metrics -->
    @if($modelInfo['trained'] && isset($modelInfo['metrics']))
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line mr-2"></i>Model Performance Metrics
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- MAE -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">MAE (Mean Absolute Error)</span>
                    <i class="fas fa-chart-bar text-blue-500"></i>
                </div>
                <p class="text-3xl font-bold text-blue-700">
                    Rp {{ number_format($modelInfo['metrics']['mae'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Rata-rata selisih prediksi</p>
            </div>

            <!-- RMSE -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">RMSE (Root Mean Squared Error)</span>
                    <i class="fas fa-chart-area text-purple-500"></i>
                </div>
                <p class="text-3xl font-bold text-purple-700">
                    Rp {{ number_format($modelInfo['metrics']['rmse'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Akar rata-rata kuadrat error</p>
            </div>

            <!-- R² Score -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">R² Score</span>
                    <i class="fas fa-percentage text-green-500"></i>
                </div>
                <p class="text-3xl font-bold text-green-700">
                    {{ number_format(($modelInfo['metrics']['r2'] ?? 0) * 100, 2) }}%
                </p>
                <p class="text-xs text-gray-500 mt-2">Akurasi model (semakin tinggi semakin baik)</p>
            </div>
        </div>

        <div class="mt-4 text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
            <p><i class="fas fa-info-circle mr-2 text-blue-500"></i><strong>Interpretasi:</strong></p>
            <ul class="list-disc list-inside ml-4 mt-2 space-y-1">
                <li><strong>MAE & RMSE:</strong> Semakin kecil nilainya, semakin akurat prediksi model</li>
                <li><strong>R² Score:</strong> Nilai mendekati 100% menunjukkan model sangat baik memprediksi harga</li>
            </ul>
        </div>
    </div>
    @endif

    <!-- Feature Importances -->
    @if($modelInfo['trained'] && isset($modelInfo['feature_importances']) && count($modelInfo['feature_importances']) > 0)
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-star mr-2"></i>Feature Importances
            </h3>
            <span class="text-sm text-gray-500">Top 10 fitur paling berpengaruh</span>
        </div>
        
        <p class="text-sm text-gray-600 mb-4">Fitur-fitur yang paling mempengaruhi prediksi harga model:</p>
        
        <div class="space-y-3">
            @php
                $topFeatures = array_slice($modelInfo['feature_importances'], 0, 10);
                $maxImportance = max(array_values($topFeatures));
            @endphp
            
            @foreach($topFeatures as $feature => $importance)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $feature }}</span>
                        <span class="text-sm text-gray-600">{{ number_format($importance * 100, 2) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2.5 rounded-full" 
                             style="width: {{ ($importance / $maxImportance) * 100 }}%">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6 flex space-x-3">
            <a href="{{ route('admin.ml.download-metrics') }}" class="text-sm text-blue-600 hover:text-blue-800">
                <i class="fas fa-download mr-1"></i>Download Metrics JSON
            </a>
            <a href="{{ route('admin.ml.download-features') }}" class="text-sm text-purple-600 hover:text-purple-800">
                <i class="fas fa-download mr-1"></i>Download Feature Importances JSON
            </a>
        </div>
    </div>
    @endif
</div>
@endsection


@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Machine Learning Model Management</h2>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Model Status -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Status Model</h3>
            
            @if($modelInfo['trained'])
                <div class="flex items-center space-x-2 mb-4">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-700 font-semibold">Model Sudah Ditraining</span>
                </div>
                <div class="text-sm text-gray-600">
                    <p>Last Modified: {{ $modelInfo['last_modified'] }}</p>
                    <p>Model Path: {{ basename($modelInfo['model_path']) }}</p>
                </div>
            @else
                <div class="flex items-center space-x-2 mb-4">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-yellow-700 font-semibold">Model Belum Ditraining</span>
                </div>
                <p class="text-sm text-gray-600">Silakan training model terlebih dahulu menggunakan dataset yang tersedia.</p>
            @endif
        </div>

        <!-- Training Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Training Model</h3>
            
            <form action="{{ route('admin.ml.train') }}" method="POST" onsubmit="return confirm('Training model akan memakan waktu. Lanjutkan?')">
                @csrf
                <p class="text-sm text-gray-600 mb-4">
                    Klik tombol di bawah untuk melatih model Random Forest menggunakan dataset yang tersedia.
                    Proses ini akan memakan waktu beberapa menit.
                </p>
                
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    {{ $modelInfo['trained'] ? 'Re-train Model' : 'Train Model' }}
                </button>
            </form>

            <!-- Training Results -->
            @if(session('training_results'))
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold mb-2">Training Results:</h4>
                    <div class="text-sm space-y-1">
                        <p>MAE: {{ number_format(session('training_results')['mae'], 2) }}</p>
                        <p>R²: {{ number_format(session('training_results')['r2'], 4) }}</p>
                        <p>Samples: {{ session('training_results')['n_samples'] }}</p>
                        <p>Features: {{ implode(', ', session('training_results')['features']) }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Test Prediction Section -->
        @if($modelInfo['trained'])
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Test Prediksi</h3>
            
            <form id="predictionForm" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Proyek</label>
                        <select name="jenis_proyek" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="pagar">Pagar</option>
                            <option value="kanopi">Kanopi</option>
                            <option value="railing">Railing</option>
                            <option value="pintu">Pintu</option>
                            <option value="teralis">Teralis</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Material</label>
                        <select name="material" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="besi_hollow">Besi Hollow</option>
                            <option value="stainless_steel">Stainless Steel</option>
                            <option value="besi_beton">Besi Beton</option>
                            <option value="aluminium">Aluminium</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Panjang (m)</label>
                        <input type="number" name="panjang" step="0.01" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lebar (m)</label>
                        <input type="number" name="lebar" step="0.01" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi (m)</label>
                        <input type="number" name="tinggi" step="0.01" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kompleksitas</label>
                        <select name="kompleksitas" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="rendah">Rendah</option>
                            <option value="sedang">Sedang</option>
                            <option value="tinggi">Tinggi</option>
                        </select>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Test Prediksi
                </button>
            </form>

            <!-- Prediction Result -->
            <div id="predictionResult" class="mt-4 p-4 bg-blue-50 rounded-lg hidden">
                <h4 class="font-semibold mb-2">Hasil Prediksi:</h4>
                <p id="predictedPrice" class="text-2xl font-bold text-blue-700"></p>
            </div>

            <div id="predictionError" class="mt-4 p-4 bg-red-50 rounded-lg hidden">
                <p class="text-red-700"></p>
            </div>
        </div>
        @endif
    </div>
</div>

@if($modelInfo['trained'])
<script>
document.getElementById('predictionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch('{{ route("admin.ml.predict") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('predictionResult').classList.remove('hidden');
            document.getElementById('predictionError').classList.add('hidden');
            document.getElementById('predictedPrice').textContent = result.formatted_price;
        } else {
            document.getElementById('predictionError').classList.remove('hidden');
            document.getElementById('predictionResult').classList.add('hidden');
            document.getElementById('predictionError').querySelector('p').textContent = result.error;
        }
    } catch (error) {
        document.getElementById('predictionError').classList.remove('hidden');
        document.getElementById('predictionResult').classList.add('hidden');
        document.getElementById('predictionError').querySelector('p').textContent = 'Terjadi kesalahan: ' + error.message;
    }
});
</script>
@endif
@endsection
