@extends('layouts.app')
@include('components.navbar')
@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Estimasi Harga Las</h2>
                        <p class="text-sm text-gray-600 mt-1">Powered by Machine Learning</p>
                    </div>
                    <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">
                        &larr; Kembali
                    </a>
                </div>

                <!-- Alert untuk hasil estimasi -->
                <div id="estimateResult" class="hidden mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-green-900">Estimasi Harga</h3>
                            <p class="text-2xl font-bold text-green-700 mt-1" id="predictedPrice"></p>
                            <p class="text-sm text-green-600 mt-1">Harga yang ditampilkan adalah harga prediksi awal. Harap diperhatikan bahwa harga dapat berubah sesuai dengan kondisi aktual di lapangan.</p>
                        </div>
                    </div>
                </div>

                <form id="estimationForm" class="space-y-6" 
                    x-data="{
                        produk: '',
                        material: '',
                        showUkuranM2: true,
                        showUkuranM: false,
                        showJumlahLubang: false,
                        finishingDisabled: false,
                        
                        handleProdukChange() {
                            if (this.produk === 'Teralis') {
                                this.showUkuranM2 = false;
                                this.showUkuranM = false;
                                this.showJumlahLubang = true;
                            } else if (this.produk === 'Railing') {
                                this.showUkuranM2 = false;
                                this.showUkuranM = true;
                                this.showJumlahLubang = false;
                            } else {
                                this.showUkuranM2 = true;
                                this.showUkuranM = false;
                                this.showJumlahLubang = false;
                            }
                        },
                        
                        handleMaterialChange() {
                            const finishingSelect = document.getElementById('finishing');
                            if (this.material.includes('Stainless')) {
                                this.finishingDisabled = true;
                                finishingSelect.value = 'Tanpa Cat';
                            } else {
                                this.finishingDisabled = false;
                            }
                        }
                    }"
                >
                    @csrf

                    <!-- Jenis Produk -->
                    <div>
                        <label for="produk" class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Produk <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="jenis_produk" 
                            name="jenis_produk" 
                            required
                            x-model="produk"
                            x-on:change="handleProdukChange()"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                            <option value="">-- Pilih Jenis Produk --</option>
                            <option value="Pagar">Pagar</option>
                            <option value="Kanopi">Kanopi</option>
                            <option value="Railing">Railing</option>
                            <option value="Teralis">Teralis (per lubang)</option>
                            <option value="Pintu Handerson">Pintu Handerson</option>
                            <option value="Pintu Gerbang">Pintu Gerbang</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih jenis produk las yang Anda butuhkan</p>
                    </div>

                    <!-- Jumlah Unit -->
                    <div>
                        <label for="jumlah_unit" class="block text-sm font-medium text-gray-700 mb-1">
                            Jumlah Unit <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="jumlah_unit" 
                            name="jumlah_unit" 
                            min="1" 
                            value="1"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                        <p class="text-xs text-gray-500 mt-1">Berapa unit/set produk yang ingin dibuat?</p>
                    </div>

                    <!-- Ukuran m² (Conditional - untuk Pagar, Kanopi, Pintu) -->
                    <div x-show="showUkuranM2" x-cloak>
                        <label for="ukuran_m2" class="block text-sm font-medium text-gray-700 mb-1">
                            Ukuran (m²) <span class="text-red-500" x-show="showUkuranM2">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="ukuran_m2" 
                            name="ukuran_m2" 
                            step="0.01" 
                            min="0.1"
                            :required="showUkuranM2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                        <p class="text-xs text-gray-500 mt-1">Masukkan total luas dalam meter persegi</p>
                    </div>

                    <!-- Ukuran m (Conditional - untuk Railing) -->
                    <div x-show="showUkuranM" x-cloak>
                        <label for="ukuran_m" class="block text-sm font-medium text-gray-700 mb-1">
                            Ukuran (m) <span class="text-red-500" x-show="showUkuranM">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="ukuran_m" 
                            name="ukuran_m" 
                            step="0.01" 
                            min="0.1"
                            :required="showUkuranM"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                        <p class="text-xs text-gray-500 mt-1">Masukkan panjang railing dalam meter</p>
                    </div>

                    <!-- Jumlah Lubang (Conditional - untuk Teralis) -->
                    <div x-show="showJumlahLubang" x-cloak>
                        <label for="jumlah_lubang" class="block text-sm font-medium text-gray-700 mb-1">
                            Jumlah Lubang <span class="text-red-500" x-show="showJumlahLubang">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="jumlah_lubang" 
                            name="jumlah_lubang" 
                            step="1"
                            min="1"
                            :required="showJumlahLubang"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-200"
                        >
                        <p class="text-xs text-gray-500 mt-1">Masukkan jumlah lubang untuk teralis</p>
                    </div>

                    <!-- Jenis Material -->
                    <div>
                        <label for="jenis_material" class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Material <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="jenis_material" 
                            name="jenis_material" 
                            required
                            x-model="material"
                            x-on:change="handleMaterialChange()"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                            <option value="">-- Pilih Material --</option>
                            <option value="Hollow">Hollow</option>
                            <option value="Hollow Stainless">Hollow Stainless</option>
                            <option value="Pipa Stainless">Pipa Stainless</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih jenis material yang akan digunakan</p>
                    </div>

                    <!-- Profile Size (REQUIRED) -->
                    <div>
                        <label for="profile_size" class="block text-sm font-medium text-gray-700 mb-1">
                            Ukuran Profile <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="profile_size" 
                            name="profile_size" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                            <option value="">-- Pilih Ukuran Profile --</option>
                            <option value="4x4">4x4 cm</option>
                            <option value="4x6">4x6 cm</option>
                            <option value="4x8">4x8 cm</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Ukuran profile material (max 4x6 untuk Railing)</p>
                    </div>

                    <!-- Ketebalan -->
                    <div>
                        <label for="ketebalan_mm" class="block text-sm font-medium text-gray-700 mb-1">
                            Ketebalan Material (mm) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="ketebalan_mm" 
                            name="ketebalan_mm" 
                            step="0.1" 
                            min="0.1"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                        <p class="text-xs text-gray-500 mt-1">Masukkan ketebalan material dalam milimeter</p>
                    </div>

                    <!-- Finishing -->
                    <div>
                        <label for="finishing" class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Finishing <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="finishing" 
                            name="finishing" 
                            required
                            :disabled="finishingDisabled"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 disabled:bg-gray-100 disabled:cursor-not-allowed"
                        >
                            <option value="">-- Pilih Finishing --</option>
                            <option value="Tanpa Cat">Tanpa Cat</option>
                            <option value="Cat Dasar">Cat Dasar</option>
                            <option value="Cat Biasa">Cat Biasa</option>
                            <option value="Cat Duco">Cat Duco</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1" x-text="finishingDisabled ? 'Material Stainless otomatis tanpa cat' : 'Pilih jenis finishing yang diinginkan'"></p>
                    </div>

                    <!-- Kerumitan Desain -->
                    <div>
                        <label for="kerumitan_desain" class="block text-sm font-medium text-gray-700 mb-1">
                            Kerumitan Desain <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="kerumitan_desain" 
                            name="kerumitan_desain" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                            <option value="">-- Pilih Kerumitan --</option>
                            <option value="Sederhana">Sederhana</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Kompleks">Kompleks</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Tingkat kerumitan desain produk</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between pt-4">
                        <button 
                            type="submit" 
                            id="submitBtn"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <i class="fas fa-calculator mr-2"></i>
                            <span>Hitung Estimasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-0.5"></i>
                <div>
                    <h3 class="font-semibold text-blue-900">Tentang Estimasi ML</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Estimasi harga dihitung menggunakan Machine Learning yang telah dilatih dengan data historis proyek sebelumnya. 
                        Harga dapat bervariasi tergantung kondisi material dan kompleksitas aktual pekerjaan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('estimationForm');
    const resultDiv = document.getElementById('estimateResult');
    const priceDisplay = document.getElementById('predictedPrice');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Set loading state - safe way without Alpine dependency
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menghitung...';
        resultDiv.classList.add('hidden');
        
        try {
            // Prepare request data dengan format yang sesuai
            const requestData = {
                jenis_produk: data.jenis_produk,
                jumlah_unit: parseInt(data.jumlah_unit) || 1,
                jumlah_lubang: data.jumlah_lubang ? parseInt(data.jumlah_lubang) : 0,
                ukuran_m2: data.ukuran_m2 ? parseFloat(data.ukuran_m2) : 0,
                ukuran_m: data.ukuran_m ? parseFloat(data.ukuran_m) : 0,
                jenis_material: data.jenis_material,
                profile_size: data.profile_size || '4x4',
                ketebalan_mm: parseFloat(data.ketebalan_mm) || 0.8,
                finishing: data.finishing,
                kerumitan_desain: data.kerumitan_desain
            };

            console.log('Sending request:', requestData); // Debug log

            const response = await fetch('{{ route("estimates.calculate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            });
            
            console.log('Response status:', response.status); // Debug log
            
            const result = await response.json();
            console.log('Response data:', result); // Debug log
            
            if (result.success) {
                // Show result
                priceDisplay.textContent = result.formatted_price || ('Rp ' + result.harga_akhir.toLocaleString('id-ID'));
                resultDiv.classList.remove('hidden');
                
                // Scroll to result
                resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                
                const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
                
                if (isAuthenticated) {
                    // If logged in, offer to save
                    if (confirm('Estimasi berhasil dihitung! Apakah Anda ingin menyimpan estimasi ini?')) {
                        // Save the estimate
                        const saveResponse = await fetch('{{ route("estimates.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ...requestData,
                                harga_akhir: result.harga_akhir
                            })
                        });
                        
                        if (saveResponse.ok) {
                            alert('Estimasi berhasil disimpan!');
                            setTimeout(() => {
                                window.location.href = '{{ route("estimates.index") }}';
                            }, 1000);
                        }
                    }
                } else {
                    // If not logged in, suggest login to save
                    if (confirm('Estimasi berhasil dihitung! Login untuk menyimpan estimasi ini?')) {
                        window.location.href = '{{ route("login") }}';
                    }
                }
            } else {
                alert('Gagal menghitung estimasi: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghitung estimasi. Silakan coba lagi.');
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-calculator mr-2"></i>Hitung Estimasi';
        }
    });
});
</script></script>

<style>
[x-cloak] {
    display: none !important;
}
</style>
@endsection
