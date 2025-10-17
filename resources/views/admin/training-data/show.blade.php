@extends('layouts.admin')

@section('title', 'Detail Data Training')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Data Training</h1>
            <p class="mt-2 text-sm text-gray-600">Informasi lengkap data training ML</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.training-data.edit', $trainingDatum->id) }}" 
               class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg flex items-center">
                <i class="fas fa-pencil-alt mr-2"></i>
                Edit
            </a>
            <form action="{{ route('admin.training-data.destroy', $trainingDatum->id) }}" 
                  method="POST" 
                  onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg flex items-center">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Product Info -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center mb-4">
                <i class="fas fa-box text-3xl mr-3"></i>
                <h2 class="text-xl font-bold">Informasi Produk</h2>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-blue-100 text-sm">Jenis Produk</p>
                    <p class="text-lg font-semibold">{{ $trainingDatum->produk }}</p>
                </div>
                <div>
                    <p class="text-blue-100 text-sm">Jumlah Unit</p>
                    <p class="text-lg font-semibold">{{ $trainingDatum->jumlah_unit }} unit</p>
                </div>
                <div>
                    <p class="text-blue-100 text-sm">Metode Hitung</p>
                    <p class="text-lg font-semibold">{{ $trainingDatum->metode_hitung }}</p>
                </div>
            </div>
        </div>

        <!-- Material & Size -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center mb-4">
                <i class="fas fa-ruler-combined text-3xl mr-3"></i>
                <h2 class="text-xl font-bold">Material & Ukuran</h2>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-green-100 text-sm">Jenis Material</p>
                    <p class="text-lg font-semibold">{{ $trainingDatum->jenis_material }}</p>
                </div>
                <div>
                    <p class="text-green-100 text-sm">Ketebalan</p>
                    <p class="text-lg font-semibold">{{ $trainingDatum->ketebalan_mm }} mm</p>
                </div>
                @if($trainingDatum->metode_hitung == 'Per m²')
                    <div>
                        <p class="text-green-100 text-sm">Ukuran</p>
                        <p class="text-lg font-semibold">{{ $trainingDatum->ukuran_m2 }} m²</p>
                    </div>
                @else
                    <div>
                        <p class="text-green-100 text-sm">Jumlah Lubang</p>
                        <p class="text-lg font-semibold">{{ $trainingDatum->jumlah_lubang }} lubang</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Design & Finish -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center mb-4">
                <i class="fas fa-paint-brush text-3xl mr-3"></i>
                <h2 class="text-xl font-bold">Desain & Finishing</h2>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-purple-100 text-sm">Finishing</p>
                    <p class="text-lg font-semibold">{{ $trainingDatum->finishing }}</p>
                </div>
                <div>
                    <p class="text-purple-100 text-sm">Kerumitan Desain</p>
                    <p class="text-lg font-semibold">{{ $trainingDatum->kerumitan_desain }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Price Card -->
    <div class="mt-6 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-lg shadow-lg p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-money-bill-wave text-white text-5xl mr-4"></i>
                <div class="text-white">
                    <p class="text-sm font-medium">Harga Akhir</p>
                    <p class="text-4xl font-bold">{{ $trainingDatum->formatted_harga }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($trainingDatum->notes)
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-sticky-note text-gray-600 mr-2"></i>
                Catatan
            </h3>
            <p class="text-gray-700">{{ $trainingDatum->notes }}</p>
        </div>
    @endif

    <!-- Metadata -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-info-circle text-gray-600 mr-2"></i>
            Informasi Tambahan
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600">Dibuat pada:</span>
                <span class="ml-2 font-semibold">{{ $trainingDatum->created_at->format('d F Y, H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-600">Terakhir diupdate:</span>
                <span class="ml-2 font-semibold">{{ $trainingDatum->updated_at->format('d F Y, H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('admin.training-data.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
