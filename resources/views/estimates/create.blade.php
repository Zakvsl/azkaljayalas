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
                            this.updateProfileSizeOptions();
                        },
                        
        handleMaterialChange() {
            const finishingSelect = document.getElementById('finishing');
            if (this.material.includes('Stainless')) {
                this.finishingDisabled = true;
                finishingSelect.value = 'Tanpa Cat';
                // Force value untuk material Stainless
                finishingSelect.setAttribute('data-force-value', 'Tanpa Cat');
            } else {
                this.finishingDisabled = false;
                finishingSelect.removeAttribute('data-force-value');
            }
            this.updateProfileSizeOptions();
            this.updateFinishingOptions();
        },                        updateProfileSizeOptions() {
                            const profileSelect = document.getElementById('profile_size');
                            let options = '<option value=\'\'>-- Pilih Ukuran Profile --</option>';
                            
                            if (this.produk === 'Teralis') {
                                options += '<option value=\'1x3\'>1x3 cm</option>';
                                options += '<option value=\'2x2\'>2x2 cm</option>';
                            } else if (this.material === 'Pipa Stainless') {
                                options += '<option value=\'1.5 inch\'>1.5 inch</option>';
                                options += '<option value=\'2 inch\'>2 inch</option>';
                            } else {
                                options += '<option value=\'4x4\'>4x4 cm</option>';
                                options += '<option value=\'4x6\'>4x6 cm</option>';
                                options += '<option value=\'4x8\'>4x8 cm</option>';
                            }
                            
                            profileSelect.innerHTML = options;
                        },
                        
                        updateFinishingOptions() {
                            const finishingSelect = document.getElementById('finishing');
                            let options = '';
                            
                            if (this.material === 'Hollow') {
                                options = '<option value=\'\'>-- Pilih Finishing --</option>';
                                options += '<option value=\'Cat Biasa\'>Cat Biasa</option>';
                                options += '<option value=\'Cat Dasar\'>Cat Dasar</option>';
                                options += '<option value=\'Cat Duco\'>Cat Duco</option>';
                                finishingSelect.disabled = false;
                            } else if (this.material.includes('Stainless')) {
                                options = '<option value=\'Tanpa Cat\' selected>Tanpa Cat</option>';
                                finishingSelect.disabled = true;
                            } else {
                                options = '<option value=\'\'>-- Pilih Finishing --</option>';
                                options += '<option value=\'Tanpa Cat\'>Tanpa Cat</option>';
                                options += '<option value=\'Cat Dasar\'>Cat Dasar</option>';
                                options += '<option value=\'Cat Biasa\'>Cat Biasa</option>';
                                options += '<option value=\'Cat Duco\'>Cat Duco</option>';
                                finishingSelect.disabled = false;
                            }
                            
                            finishingSelect.innerHTML = options;
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
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-show="produk === 'Teralis'">Teralis: 1x3 atau 2x2</span>
                            <span x-show="produk !== 'Teralis' && material === 'Pipa Stainless'">Pipa Stainless: 1.5 inch atau 2 inch</span>
                            <span x-show="produk !== 'Teralis' && material !== 'Pipa Stainless'">Hollow: 4x4, 4x6, atau 4x8</span>
                        </p>
                    </div>

                    <!-- Ketebalan -->
                    <div>
                        <label for="ketebalan_mm" class="block text-sm font-medium text-gray-700 mb-1">
                            Ketebalan Material (mm) <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="ketebalan_mm" 
                            name="ketebalan_mm" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        >
                            <option value="">-- Pilih Ketebalan --</option>
                            <option value="0.8">0.8 mm</option>
                            <option value="1.0">1.0 mm</option>
                            <option value="1.2">1.2 mm</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih ketebalan material yang tersedia</p>
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
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-show="material.includes('Stainless')">Material Stainless otomatis tanpa cat</span>
                            <span x-show="material === 'Hollow'">Hollow: Cat Biasa, Cat Dasar, atau Cat Duco</span>
                            <span x-show="!material">Pilih jenis finishing yang diinginkan</span>
                        </p>
                    </div>

                    <!-- Kerumitan Desain -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="kerumitan_desain" class="block text-sm font-medium text-gray-700">
                                Kerumitan Desain <span class="text-red-500">*</span>
                            </label>
                            <button 
                                type="button" 
                                onclick="toggleComplexityInfo()"
                                class="text-blue-600 hover:text-blue-800 focus:outline-none transition-colors"
                                title="Lihat detail spesifikasi"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
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
                        <p class="text-xs text-gray-500 mt-1">Klik ikon <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg> untuk melihat detail spesifikasi</p>
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

                <!-- Alert untuk hasil estimasi (DIPINDAH KE BAWAH FORM) -->
                <div id="estimateResult" class="hidden mt-6 p-6 rounded-lg bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 shadow-lg">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-600 text-3xl mr-4 mt-1"></i>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xl font-bold text-green-900">Hasil Estimasi Harga</h3>
                                <span id="predictionBadge" class="px-3 py-1 text-xs font-semibold rounded-full"></span>
                            </div>
                            <p class="text-3xl font-bold text-green-700 mb-3" id="predictedPrice"></p>
                            <div id="mlInfoBox" class="bg-white bg-opacity-60 rounded-md p-3 border border-green-200 mb-3">
                                <p class="text-sm text-green-800">
                                    <i class="fas fa-robot mr-1"></i>
                                    <span id="predictionMessage">Prediksi menggunakan Machine Learning dengan akurasi 97.3%</span>
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-60 rounded-md p-3 border border-green-200">
                                <p class="text-sm text-green-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Harga yang ditampilkan adalah estimasi awal. 
                                    Harga final dapat berubah sesuai dengan kondisi aktual di lapangan setelah survei.
                                </p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="mt-4 flex flex-wrap gap-3">
                                <a href="{{ route('survey.create') }}" 
                                   id="bookingSurveyBtn"
                                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200 shadow-md">
                                    <i class="fas fa-calendar-check mr-2"></i>
                                    Booking Survei Lokasi
                                </a>
                                <button type="button" 
                                        onclick="document.getElementById('estimationForm').reset(); document.getElementById('estimateResult').classList.add('hidden');"
                                        class="inline-flex items-center px-6 py-3 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition duration-200">
                                    <i class="fas fa-redo mr-2"></i>
                                    Hitung Ulang
                                </button>
                                <a href="{{ route('home') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition duration-200">
                                    <i class="fas fa-home mr-2"></i>
                                    Kembali ke Beranda
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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

<!-- Modal Detail Kerumitan Desain -->
<div id="complexityModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between border-b pb-3 mb-4">
            <h3 class="text-xl font-bold text-gray-900">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Spesifikasi Kerumitan Desain
            </h3>
            <button 
                onclick="toggleComplexityInfo()" 
                class="text-gray-400 hover:text-gray-600 transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <!-- Sederhana -->
            <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                <div class="flex items-center mb-2">
                    <span class="px-3 py-1 bg-green-600 text-white text-sm font-semibold rounded-full">Sederhana</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Desain Minimalis & Standar</h4>
                <ul class="space-y-1 text-sm text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-600 mr-2 mt-1"></i>
                        <span>Pola lurus atau geometris sederhana</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-600 mr-2 mt-1"></i>
                        <span>Minim ornamen atau detail tambahan</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-600 mr-2 mt-1"></i>
                        <span>Waktu pengerjaan cepat</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-600 mr-2 mt-1"></i>
                        <span>Cocok untuk: Pagar minimalis, teralis standar, railing sederhana</span>
                    </li>
                </ul>
            </div>

            <!-- Menengah -->
            <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                <div class="flex items-center mb-2">
                    <span class="px-3 py-1 bg-yellow-600 text-white text-sm font-semibold rounded-full">Menengah</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Desain dengan Detail Moderat</h4>
                <ul class="space-y-1 text-sm text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                        <span>Kombinasi pola lurus dan lengkung</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                        <span>Ada beberapa ornamen dekoratif</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                        <span>Detailing tambahan seperti ulir atau motif sederhana</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                        <span>Waktu pengerjaan sedang</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                        <span>Cocok untuk: Pagar dengan motif, kanopi dengan ornamen, pintu gerbang semi-custom</span>
                    </li>
                </ul>
            </div>

            <!-- Kompleks -->
            <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                <div class="flex items-center mb-2">
                    <span class="px-3 py-1 bg-red-600 text-white text-sm font-semibold rounded-full">Kompleks</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Desain Custom & Artistik</h4>
                <ul class="space-y-1 text-sm text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check text-red-600 mr-2 mt-1"></i>
                        <span>Pola rumit dengan banyak lengkungan</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-red-600 mr-2 mt-1"></i>
                        <span>Ornamen artistik dan custom design</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-red-600 mr-2 mt-1"></i>
                        <span>Detail ukiran, motif flora/fauna, atau logo custom</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-red-600 mr-2 mt-1"></i>
                        <span>Teknik las yang presisi dan tingkat kesulitan tinggi</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-red-600 mr-2 mt-1"></i>
                        <span>Waktu pengerjaan lama</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-red-600 mr-2 mt-1"></i>
                        <span>Cocok untuk: Pintu gerbang mewah, pagar artistik, kanopi custom dengan ornamen rumit</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button 
                onclick="toggleComplexityInfo()" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
            >
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
// Function to toggle complexity info modal
function toggleComplexityInfo() {
    const modal = document.getElementById('complexityModal');
    modal.classList.toggle('hidden');
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('complexityModal');
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            toggleComplexityInfo();
        }
    });

    const form = document.getElementById('estimationForm');
    const resultDiv = document.getElementById('estimateResult');
    const priceDisplay = document.getElementById('predictedPrice');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        console.log('=== Form Data Debug ===');
        console.log('Raw form data:', data);
        
        // Set loading state - safe way without Alpine dependency
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menghitung...';
        resultDiv.classList.add('hidden');
        
        try {
            // Prepare request data dengan format yang sesuai
            // Get finishing value (force value jika disabled/Stainless)
            const finishingSelect = document.getElementById('finishing');
            const finishingValue = finishingSelect.getAttribute('data-force-value') || data.finishing || 'Tanpa Cat';
            
            const requestData = {
                jenis_produk: data.jenis_produk,
                jumlah_unit: parseInt(data.jumlah_unit) || 1,
                jumlah_lubang: data.jumlah_lubang ? parseInt(data.jumlah_lubang) : 0,
                ukuran_m2: data.ukuran_m2 ? parseFloat(data.ukuran_m2) : 0,
                ukuran_m: data.ukuran_m ? parseFloat(data.ukuran_m) : 0,
                jenis_material: data.jenis_material,
                profile_size: data.profile_size || '4x4',
                ketebalan_mm: parseFloat(data.ketebalan_mm) || 0.8,
                finishing: finishingValue,
                kerumitan_desain: data.kerumitan_desain
            };

            console.log('📤 Sending request:', requestData);

            const response = await fetch('{{ route("estimates.calculate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            });
            
            console.log('📥 Response status:', response.status);
            
            const result = await response.json();
            console.log('📥 Response data:', result);
            
            if (result.success) {
                // Show result
                priceDisplay.textContent = result.formatted_price || ('Rp ' + result.harga_akhir.toLocaleString('id-ID'));
                resultDiv.classList.remove('hidden');
                
                // Update prediction method badge and message
                const badge = document.getElementById('predictionBadge');
                const message = document.getElementById('predictionMessage');
                const mlInfoBox = document.getElementById('mlInfoBox');
                
                if (result.prediction_method === 'ml') {
                    badge.textContent = '🤖 Machine Learning';
                    badge.className = 'px-3 py-1 text-xs font-semibold rounded-full bg-blue-500 text-white';
                    message.textContent = result.message || 'Prediksi menggunakan Machine Learning dengan akurasi 97.3%';
                    mlInfoBox.className = 'bg-blue-50 rounded-md p-3 border border-blue-200 mb-3';
                } else {
                    badge.textContent = '📊 Formula Standar';
                    badge.className = 'px-3 py-1 text-xs font-semibold rounded-full bg-yellow-500 text-white';
                    message.textContent = result.message || 'Prediksi menggunakan formula standar (ML tidak tersedia)';
                    mlInfoBox.className = 'bg-yellow-50 rounded-md p-3 border border-yellow-200 mb-3';
                }
                
                // Scroll to result (smooth scroll ke bawah)
                setTimeout(() => {
                    resultDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 100);
                
                const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
                
                if (isAuthenticated) {
                    // Auto-save estimasi untuk user yang sudah login
                    try {
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
                            const saveResult = await saveResponse.json();
                            console.log('Estimasi tersimpan:', saveResult);
                            
                            // Simpan data estimasi ke localStorage untuk auto-fill survey
                            const estimateData = {
                                estimate_id: saveResult.estimate_id,
                                jenis_produk: requestData.jenis_produk,
                                jenis_material: requestData.jenis_material,
                                profile_size: requestData.profile_size,
                                ketebalan_mm: requestData.ketebalan_mm,
                                ukuran_m2: requestData.ukuran_m2,
                                jumlah_lubang: requestData.jumlah_lubang,
                                jumlah_unit: requestData.jumlah_unit,
                                finishing: requestData.finishing,
                                harga_akhir: result.harga_akhir
                            };
                            localStorage.setItem('latestEstimate', JSON.stringify(estimateData));
                            console.log('💾 Data estimasi disimpan ke localStorage:', estimateData);
                            
                            // Update booking survey button dengan estimate_id
                            if (saveResult.estimate_id) {
                                const bookingBtn = document.getElementById('bookingSurveyBtn');
                                bookingBtn.href = '{{ route("survey.create") }}?estimate_id=' + saveResult.estimate_id;
                            }
                            
                            // Tampilkan notifikasi sukses
                            setTimeout(() => {
                                if (confirm('✅ Estimasi berhasil disimpan!\n\n💡 Apakah Anda ingin melanjutkan dengan booking survei lokasi untuk mendapatkan harga yang lebih akurat?')) {
                                    window.location.href = document.getElementById('bookingSurveyBtn').href;
                                }
                            }, 500);
                        }
                    } catch (saveError) {
                        console.error('Error saving estimate:', saveError);
                    }
                } else {
                    // Jika belum login, arahkan ke login dulu
                    setTimeout(() => {
                        if (confirm('💡 Untuk menyimpan estimasi dan booking survei, silakan login terlebih dahulu.\n\nApakah Anda ingin login sekarang?')) {
                            window.location.href = '{{ route("login") }}';
                        }
                    }, 500);
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
