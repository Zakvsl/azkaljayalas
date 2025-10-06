@extends('layouts.admin')

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
