@extends('layouts.admin')

@section('title', 'Tambah Data Training')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Tambah Data Training</h1>
        <p class="mt-2 text-sm text-gray-600">Masukkan data training untuk meningkatkan akurasi model ML</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.training-data.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Produk -->
                <div>
                    <label for="produk" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Produk <span class="text-red-600">*</span>
                    </label>
                    <select name="produk" id="produk" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('produk') border-red-500 @enderror">
                        <option value="">Pilih Produk</option>
                        <option value="Pagar" {{ old('produk') == 'Pagar' ? 'selected' : '' }}>Pagar</option>
                        <option value="Kanopi" {{ old('produk') == 'Kanopi' ? 'selected' : '' }}>Kanopi</option>
                        <option value="Railing" {{ old('produk') == 'Railing' ? 'selected' : '' }}>Railing</option>
                        <option value="Teralis" {{ old('produk') == 'Teralis' ? 'selected' : '' }}>Teralis</option>
                        <option value="Pintu" {{ old('produk') == 'Pintu' ? 'selected' : '' }}>Pintu</option>
                    </select>
                    @error('produk')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah Unit -->
                <div>
                    <label for="jumlah_unit" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Unit <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="jumlah_unit" id="jumlah_unit" value="{{ old('jumlah_unit', 1) }}" 
                           min="1" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jumlah_unit') border-red-500 @enderror">
                    @error('jumlah_unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Metode Hitung -->
                <div>
                    <label for="metode_hitung" class="block text-sm font-medium text-gray-700 mb-2">
                        Metode Hitung <span class="text-red-600">*</span>
                    </label>
                    <select name="metode_hitung" id="metode_hitung" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('metode_hitung') border-red-500 @enderror"
                            onchange="toggleMetodeHitung()">
                        <option value="">Pilih Metode</option>
                        <option value="Per m²" {{ old('metode_hitung') == 'Per m²' ? 'selected' : '' }}>Per m²</option>
                        <option value="Per Lubang" {{ old('metode_hitung') == 'Per Lubang' ? 'selected' : '' }}>Per Lubang</option>
                    </select>
                    @error('metode_hitung')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ukuran m² -->
                <div id="ukuran_m2_wrapper">
                    <label for="ukuran_m2" class="block text-sm font-medium text-gray-700 mb-2">
                        Ukuran (m²)
                    </label>
                    <input type="number" name="ukuran_m2" id="ukuran_m2" value="{{ old('ukuran_m2') }}" 
                           step="0.01" min="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ukuran_m2') border-red-500 @enderror">
                    @error('ukuran_m2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah Lubang -->
                <div id="jumlah_lubang_wrapper">
                    <label for="jumlah_lubang" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Lubang
                    </label>
                    <input type="number" name="jumlah_lubang" id="jumlah_lubang" value="{{ old('jumlah_lubang') }}" 
                           step="0.1" min="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jumlah_lubang') border-red-500 @enderror">
                    @error('jumlah_lubang')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Material -->
                <div>
                    <label for="jenis_material" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Material <span class="text-red-600">*</span>
                    </label>
                    <select name="jenis_material" id="jenis_material" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jenis_material') border-red-500 @enderror">
                        <option value="">Pilih Material</option>
                        <option value="Hollow" {{ old('jenis_material') == 'Hollow' ? 'selected' : '' }}>Hollow</option>
                        <option value="Besi" {{ old('jenis_material') == 'Besi' ? 'selected' : '' }}>Besi</option>
                        <option value="Stainless" {{ old('jenis_material') == 'Stainless' ? 'selected' : '' }}>Stainless</option>
                    </select>
                    @error('jenis_material')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ketebalan -->
                <div>
                    <label for="ketebalan_mm" class="block text-sm font-medium text-gray-700 mb-2">
                        Ketebalan (mm) <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="ketebalan_mm" id="ketebalan_mm" value="{{ old('ketebalan_mm') }}" 
                           step="0.1" min="0" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ketebalan_mm') border-red-500 @enderror">
                    @error('ketebalan_mm')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Finishing -->
                <div>
                    <label for="finishing" class="block text-sm font-medium text-gray-700 mb-2">
                        Finishing <span class="text-red-600">*</span>
                    </label>
                    <select name="finishing" id="finishing" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('finishing') border-red-500 @enderror">
                        <option value="">Pilih Finishing</option>
                        <option value="Cat" {{ old('finishing') == 'Cat' ? 'selected' : '' }}>Cat</option>
                        <option value="Powder Coating" {{ old('finishing') == 'Powder Coating' ? 'selected' : '' }}>Powder Coating</option>
                        <option value="Tanpa Finishing" {{ old('finishing') == 'Tanpa Finishing' ? 'selected' : '' }}>Tanpa Finishing</option>
                    </select>
                    @error('finishing')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kerumitan Desain -->
                <div>
                    <label for="kerumitan_desain" class="block text-sm font-medium text-gray-700 mb-2">
                        Kerumitan Desain <span class="text-red-600">*</span>
                    </label>
                    <select name="kerumitan_desain" id="kerumitan_desain" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kerumitan_desain') border-red-500 @enderror">
                        <option value="">Pilih Kerumitan</option>
                        <option value="Sederhana" {{ old('kerumitan_desain') == 'Sederhana' ? 'selected' : '' }}>Sederhana</option>
                        <option value="Menengah" {{ old('kerumitan_desain') == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                        <option value="Kompleks" {{ old('kerumitan_desain') == 'Kompleks' ? 'selected' : '' }}>Kompleks</option>
                    </select>
                    @error('kerumitan_desain')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Akhir -->
                <div class="md:col-span-2">
                    <label for="harga_akhir" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Akhir (Rp) <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="harga_akhir" id="harga_akhir" value="{{ old('harga_akhir') }}" 
                           min="0" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('harga_akhir') border-red-500 @enderror">
                    @error('harga_akhir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.training-data.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleMetodeHitung() {
    const metode = document.getElementById('metode_hitung').value;
    const ukuranWrapper = document.getElementById('ukuran_m2_wrapper');
    const lubangWrapper = document.getElementById('jumlah_lubang_wrapper');
    
    if (metode === 'Per m²') {
        ukuranWrapper.style.display = 'block';
        lubangWrapper.style.display = 'none';
        document.getElementById('jumlah_lubang').value = 0;
    } else if (metode === 'Per Lubang') {
        ukuranWrapper.style.display = 'none';
        lubangWrapper.style.display = 'block';
        document.getElementById('ukuran_m2').value = 0;
    } else {
        ukuranWrapper.style.display = 'block';
        lubangWrapper.style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleMetodeHitung();
});
</script>
@endsection
