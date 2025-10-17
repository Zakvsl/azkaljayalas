@extends('layouts.app')

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Estimasi Harga Las</h2>
                    <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">
                        &larr; Kembali
                    </a>
                </div>

                <form id="estimationForm" class="space-y-6" data-is-guest="{{ auth()->guest() ? 'true' : 'false' }}" 
                    x-data="{
                        jenis_produk: '',
                        jenis_material: '',
                        showLubang: false,
                        showUkuran: false,
                        showProfileSize: false,
                        
                        handleProdukChange() {
                            this.showLubang = this.jenis_produk === 'Teralis';
                            this.showUkuran = this.jenis_produk !== 'Teralis' && this.jenis_produk !== '';
                        },
                        
                        handleMaterialChange() {
                            const materialsWithProfile = ['hollow', 'besi_siku', 'aluminium', 'stainless'];
                            this.showProfileSize = materialsWithProfile.includes(this.jenis_material);
                        }
                    }"
                >
                    @csrf

                    <div>
                        <label for="jenis_produk" class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Produk <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="jenis_produk" 
                            name="jenis_produk" 
                            required
                            x-model="jenis_produk"
                            x-on:change="handleProdukChange()"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                            <option value="">-- Pilih Jenis Produk --</option>
                            <option value="Pagar">Pagar</option>
                            <option value="Kanopi">Kanopi</option>
                            <option value="Railing">Railing</option>
                            <option value="Teralis">Teralis</option>
                            <option value="Pintu">Pintu</option>
                            <option value="Tangga">Tangga</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih jenis produk las yang Anda butuhkan</p>
                    </div>

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

                    <div x-show="showLubang" x-cloak>
                        <label for="jumlah_lubang" class="block text-sm font-medium text-gray-700 mb-1">
                            Jumlah Lubang <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="jumlah_lubang" 
                            name="jumlah_lubang" 
                            min="1"
                            x-bind:required="showLubang"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                        <p class="text-xs text-gray-500 mt-1">Untuk teralis, harga dihitung per lubang</p>
                    </div>

                    <div x-show="showUkuran" x-cloak>
                        <label for="ukuran_m2" class="block text-sm font-medium text-gray-700 mb-1">
                            Ukuran (m²) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="ukuran_m2" 
                            name="ukuran_m2" 
                            step="0.01" 
                            min="0.1"
                            x-bind:required="showUkuran"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                        <p class="text-xs text-gray-500 mt-1">Luas area dalam meter persegi (contoh: pagar 2m x 1.5m = 3 m²)</p>
                    </div>

                    <div>
                        <label for="jenis_material" class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Material <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="jenis_material" 
                            name="jenis_material" 
                            required
                            x-model="jenis_material"
                            x-on:change="handleMaterialChange()"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                            <option value="">-- Pilih Jenis Material --</option>
                            <option value="hollow">Hollow (Besi Kotak)</option>
                            <option value="besi_siku">Besi Siku</option>
                            <option value="aluminium">Aluminium</option>
                            <option value="stainless">Stainless Steel</option>
                            <option value="plat">Plat Besi</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Material utama yang akan digunakan</p>
                    </div>

                    <div x-show="showProfileSize" x-cloak>
                        <label for="profile_size" class="block text-sm font-medium text-gray-700 mb-1">
                            Ukuran Profile <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="profile_size" 
                            name="profile_size"
                            placeholder="contoh: 2x4, 3x3, 1.5inch"
                            x-bind:required="showProfileSize"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                        <p class="text-xs text-gray-500 mt-1">Ukuran profile material (contoh: 2x4 untuk hollow 2x4 cm)</p>
                    </div>

                    <div>
                        <label for="ketebalan_mm" class="block text-sm font-medium text-gray-700 mb-1">
                            Ketebalan (mm) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="ketebalan_mm" 
                            name="ketebalan_mm" 
                            step="0.1" 
                            min="0.1"
                            required
                            placeholder="contoh: 1.2, 2.0, 3.0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                        <p class="text-xs text-gray-500 mt-1">Ketebalan material dalam milimeter</p>
                    </div>

                    <div>
                        <label for="finishing" class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Finishing <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="finishing" 
                            name="finishing" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                            <option value="cat_biasa">Cat Biasa</option>
                            <option value="cat_epoxy">Cat Epoxy</option>
                            <option value="powder_coating">Powder Coating</option>
                            <option value="galvanis">Galvanis</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Jenis finishing untuk perlindungan dan estetika</p>
                    </div>

                    <div>
                        <label for="kerumitan_desain" class="block text-sm font-medium text-gray-700 mb-1">
                            Tingkat Kerumitan Desain <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="kerumitan_desain" 
                            name="kerumitan_desain" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                            <option value="1">Sederhana (Desain standar, bentuk simpel)</option>
                            <option value="2">Menengah (Desain custom, sedikit detail)</option>
                            <option value="3">Kompleks (Desain rumit, banyak detail ornamen)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Tingkat kesulitan desain mempengaruhi waktu pengerjaan dan harga</p>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan Tambahan (Opsional)
                        </label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            rows="3"
                            placeholder="Tambahkan informasi atau permintaan khusus Anda..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <button 
                            type="button"
                            onclick="calculateEstimate()"
                            class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-semibold shadow-md flex items-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Hitung Estimasi
                        </button>

                        <button 
                            type="button"
                            onclick="saveEstimate()"
                            id="saveBtn"
                            disabled
                            class="bg-gray-300 text-gray-500 px-6 py-2.5 rounded-lg cursor-not-allowed font-semibold"
                        >
                            Simpan Estimasi
                        </button>
                    </div>

                    <div id="estimateResult" class="hidden mt-6 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Hasil Estimasi Harga</h3>
                        <div class="flex items-baseline">
                            <span class="text-sm text-gray-600 mr-2">Rp</span>
                            <p id="estimatedPrice" class="text-4xl font-bold text-blue-600"></p>
                        </div>
                        <div class="mt-4 p-4 bg-white rounded-lg">
                            <p class="text-sm text-gray-600">
                                <strong>Catatan:</strong> Harga di atas adalah estimasi berdasarkan perhitungan sistem. 
                                Harga aktual dapat berbeda tergantung kondisi material, lokasi, dan kebutuhan spesifik proyek Anda.
                            </p>
                        </div>
                    </div>

                    <div id="loadingIndicator" class="hidden mt-6 text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="text-gray-600 mt-2">Sedang menghitung estimasi...</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentEstimate = null;

async function calculateEstimate() {
    const form = document.getElementById('estimationForm');
    const formData = new FormData(form);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    document.getElementById('loadingIndicator').classList.remove('hidden');
    document.getElementById('estimateResult').classList.add('hidden');
    
    try {
        const response = await fetch('{{ route("estimates.calculate") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok) {
            currentEstimate = data.harga_akhir;
            const formattedPrice = new Intl.NumberFormat('id-ID').format(data.harga_akhir);
            document.getElementById('estimatedPrice').textContent = formattedPrice;
            document.getElementById('estimateResult').classList.remove('hidden');
            
            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = false;
            saveBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            saveBtn.classList.add('bg-green-600', 'text-white', 'hover:bg-green-700', 'cursor-pointer');
        } else {
            alert('Error: ' + (data.message || 'Terjadi kesalahan saat menghitung estimasi'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
    } finally {
        document.getElementById('loadingIndicator').classList.add('hidden');
    }
}

async function saveEstimate() {
    if (!currentEstimate) {
        alert('Silakan hitung estimasi terlebih dahulu');
        return;
    }

    const isGuest = document.getElementById('estimationForm').dataset.isGuest === 'true';
    if (isGuest) {
        if (confirm('Anda harus login untuk menyimpan estimasi. Lanjutkan ke halaman login?')) {
            window.location.href = '{{ route("login") }}';
        }
        return;
    }

    const form = document.getElementById('estimationForm');
    const formData = new FormData(form);
    formData.append('harga_akhir', currentEstimate);

    try {
        const response = await fetch('{{ route("estimates.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok) {
            alert('Estimasi berhasil disimpan!');
            window.location.href = '{{ route("estimates.index") }}';
        } else {
            alert('Error: ' + (data.message || 'Gagal menyimpan estimasi'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection