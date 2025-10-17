@extends('layouts.admin')

@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Pesanan
        </a>
        <div class="flex space-x-2">
            <a href="{{ route('admin.orders.edit', $order) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition duration-150">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <form action="{{ route('admin.orders.destroy', $order) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-sm transition duration-150">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Information -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user text-blue-600 mr-2"></i>
                Informasi Pelanggan
            </h3>
            <div class="space-y-3">
                <div>
                    <dt class="text-xs font-medium text-gray-600 uppercase">Nama</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $order->customer_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-600 uppercase">Telepon</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="tel:{{ $order->phone }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-phone mr-1"></i>{{ $order->phone }}
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-600 uppercase">Alamat</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->address }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-600 uppercase">Tanggal Pesan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->order_date->format('d F Y') }}</dd>
                </div>
                @if($order->completion_date)
                <div>
                    <dt class="text-xs font-medium text-gray-600 uppercase">Tanggal Selesai</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->completion_date->format('d F Y') }}</dd>
                </div>
                @endif
            </div>
        </div>

        <!-- Project Details -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-sm p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-clipboard-list text-green-600 mr-2"></i>
                Detail Proyek
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-xs font-medium text-gray-600 uppercase">Jenis Proyek</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ ucfirst(str_replace('_', ' ', $order->project_type)) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-600 uppercase">Material</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->material_type)) }}</dd>
                </div>

                @if($order->dimensions)
                    <div class="md:col-span-2">
                        <dt class="text-xs font-medium text-gray-600 uppercase">Dimensi</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="flex flex-wrap gap-4">
                                @if(isset($order->dimensions['length']))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white text-gray-700">
                                        <i class="fas fa-ruler-horizontal mr-1"></i> Panjang: {{ $order->dimensions['length'] }} m
                                    </span>
                                @endif
                                @if(isset($order->dimensions['width']))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white text-gray-700">
                                        <i class="fas fa-ruler-vertical mr-1"></i> Lebar: {{ $order->dimensions['width'] }} m
                                    </span>
                                @endif
                                @if(isset($order->dimensions['height']))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white text-gray-700">
                                        <i class="fas fa-arrows-alt-v mr-1"></i> Tinggi: {{ $order->dimensions['height'] }} m
                                    </span>
                                @endif
                                @if(isset($order->dimensions['thickness']))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white text-gray-700">
                                        <i class="fas fa-ruler mr-1"></i> Ketebalan: {{ $order->dimensions['thickness'] }} mm
                                    </span>
                                @endif
                            </div>
                        </dd>
                    </div>
                @endif

                @if($order->description)
                <div class="md:col-span-2">
                    <dt class="text-xs font-medium text-gray-600 uppercase">Deskripsi</dt>
                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap bg-white p-3 rounded-md">{{ $order->description }}</dd>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pricing and Status -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 uppercase mb-2">Harga Estimasi</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $order->formatted_estimated_price }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 uppercase mb-2">Harga Aktual</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $order->formatted_actual_price }}</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 uppercase mb-2">Status</h3>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->status_badge }}">
                {{ $order->status_label }}
            </span>
        </div>
    </div>

    @if($order->notes)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-sticky-note text-orange-600 mr-2"></i>
            Catatan
        </h3>
        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $order->notes }}</p>
    </div>
    @endif

    <!-- Timeline -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-history text-gray-600 mr-2"></i>
            Timeline
        </h3>
        <div class="space-y-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-plus text-blue-600 text-xs"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Pesanan Dibuat</p>
                    <p class="text-xs text-gray-500">{{ $order->created_at->format('d F Y, H:i') }}</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-clock text-gray-600 text-xs"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Terakhir Diperbarui</p>
                    <p class="text-xs text-gray-500">{{ $order->updated_at->format('d F Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
