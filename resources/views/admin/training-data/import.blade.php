@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        <i class="fas fa-file-upload mr-2"></i>Import Data Training
                    </h1>
                    <p class="text-gray-600 mt-2">Upload file CSV untuk menambah data training secara massal</p>
                </div>
                <a href="{{ route('admin.training-data.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">
                <i class="fas fa-info-circle mr-2"></i>Instruksi Format CSV
            </h3>
            <div class="space-y-2 text-blue-900">
                <p><strong>1. Format File:</strong> File harus berformat CSV (.csv)</p>
                <p><strong>2. Baris Header:</strong> Baris pertama harus berisi nama kolom</p>
                <p><strong>3. Format Kolom Fleksibel:</strong> <span class="bg-green-100 px-2 py-1 rounded text-green-800 font-semibold">✓ Sistem auto-detect</span> - Nama kolom bisa pakai underscore (Jumlah_Unit) atau spasi (Jumlah Unit), huruf besar/kecil bebas</p>
                <p><strong>4. Kolom Tambahan:</strong> <span class="bg-green-100 px-2 py-1 rounded text-green-800 font-semibold">✓ Boleh ada kolom tambahan</span> seperti ID, No Transaksi, Tanggal, dll. Sistem akan otomatis mengabaikan kolom yang tidak diperlukan.</p>
                <p><strong>5. Normalisasi Otomatis:</strong> <span class="bg-blue-100 px-2 py-1 rounded text-blue-800 font-semibold">✓ Auto-mapping</span> - Sistem akan otomatis menormalisasi nilai (contoh: "Besi Siku" → "Besi", "Pintu Gerbang" → "Pintu")</p>
                <p><strong>6. Kolom yang Diperlukan (urutan bebas):</strong></p>
                <ul class="list-disc list-inside ml-4 space-y-1">
                    <li><code class="bg-blue-100 px-2 py-1 rounded">Produk</code> - Pagar, Kanopi, Railing, Teralis, atau Pintu</li>
                    <li><code class="bg-blue-100 px-2 py-1 rounded">Jumlah_Unit</code> - Angka (minimal 1)</li>
                    <li><code class="bg-blue-100 px-2 py-1 rounded">Jumlah_Lubang</code> - Angka (0 jika tidak menggunakan)</li>
                    <li><code class="bg-blue-100 px-2 py-1 rounded">Ukuran_m2</code> - Angka desimal (0 jika tidak menggunakan)</li>
                    <li><code class="bg-blue-100 px-2 py-1 rounded">Jenis_Material</code> - Hollow, Besi, atau Stainless</li>
                    <li><code class="bg-blue-100 px-2 py-1 rounded">Ketebalan_mm</code> - Angka desimal (minimal 0.1)</li>
                    <li><code class="bg-blue-100 px-2 py-1 rounded">Finishing</code> - Cat, Powder Coating, atau Tanpa Finishing</li>
                    <li><code class="bg-blue-100 px-2 py-1 rounded">Kerumitan_Desain</code> - Sederhana, Menengah, atau Kompleks</li>
                    <li><code class="bg-blue-100 px-2 py-1 rounded">Metode_Hitung</code> - Per m² atau Per Lubang</li>
                    <li><code class="bg-blue-100 px-2 py-1 rounded">Harga_Akhir</code> - Angka (tanpa titik/koma pemisah)</li>
                </ul>
                <p class="mt-3"><strong>Catatan:</strong> Jika menggunakan Metode_Hitung "Per m²", isi Ukuran_m2 dan set Jumlah_Lubang = 0. Jika "Per Lubang", sebaliknya.</p>
            </div>
        </div>

        <!-- Download Sample Template -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-semibold text-green-800"><i class="fas fa-download mr-2"></i>Template CSV</h4>
                    <p class="text-sm text-green-700 mt-1">Download data training yang sudah ada sebagai template/contoh format yang benar</p>
                    <p class="text-xs text-green-600 mt-1"><i class="fas fa-lightbulb mr-1"></i>Tip: Anda bisa menambahkan kolom ID atau timestamp di file CSV, sistem akan otomatis mengabaikannya</p>
                </div>
                <a href="{{ route('admin.training-data.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-file-csv mr-2"></i>Download Template
                </a>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('admin.training-data.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- File Input -->
                <div class="mb-6">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file-csv mr-1"></i>Pilih File CSV
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label for="file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                                <p class="text-xs text-gray-500">CSV (MAX. 10MB)</p>
                                <p class="text-xs text-gray-400 mt-2" id="file-name"></p>
                            </div>
                            <input id="file" name="file" type="file" accept=".csv" class="hidden" required onchange="updateFileName(this)" />
                        </label>
                    </div>
                    @error('file')
                        <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.training-data.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-upload mr-2"></i>Import Data
                    </button>
                </div>
            </form>
        </div>

        <!-- Error Display -->
        @if(session('errors_list') && count(session('errors_list')) > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mt-6">
            <h3 class="text-lg font-semibold text-red-800 mb-3">
                <i class="fas fa-exclamation-triangle mr-2"></i>Error saat Import ({{ count(session('errors_list')) }} baris gagal)
            </h3>
            <div class="max-h-64 overflow-y-auto">
                <ul class="list-disc list-inside space-y-2 text-red-900 text-sm">
                    @foreach(session('errors_list') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function updateFileName(input) {
    const fileNameDisplay = document.getElementById('file-name');
    if (input.files.length > 0) {
        const fileName = input.files[0].name;
        const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2); // MB
        fileNameDisplay.textContent = `File terpilih: ${fileName} (${fileSize} MB)`;
        fileNameDisplay.classList.add('text-blue-600', 'font-semibold');
    } else {
        fileNameDisplay.textContent = '';
    }
}
</script>
@endsection
