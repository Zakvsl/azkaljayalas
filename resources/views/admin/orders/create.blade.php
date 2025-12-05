@extends('layouts.admin')

@section('title', 'Tambah Pesanan')
@section('page-title', 'Tambah Pesanan Baru')

@section('content')
<div class="space-y-6">
    <div class="flex items-center">
        <a href="{{ route('admin.orders.index') }}" 
           class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.orders.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Customer Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pelanggan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="customer_name" 
                               id="customer_name" 
                               value="{{ old('customer_name') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('customer_name') border-red-300 @enderror">
                        @error('customer_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            No. Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="phone" 
                               id="phone" 
                               value="{{ old('phone') }}"
                               required
                               placeholder="08xxxxxxxxxx"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-300 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" 
                                  id="address" 
                                  rows="3"
                                  required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('address') border-red-300 @enderror">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Project Details -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Proyek</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="project_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Proyek <span class="text-red-500">*</span>
                        </label>
                        <select name="project_type" 
                                id="project_type" 
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('project_type') border-red-300 @enderror">
                            <option value="">Pilih Jenis Proyek</option>
                            <option value="pagar" {{ old('project_type') == 'pagar' ? 'selected' : '' }}>Pagar</option>
                            <option value="kanopi" {{ old('project_type') == 'kanopi' ? 'selected' : '' }}>Kanopi</option>
                            <option value="railing" {{ old('project_type') == 'railing' ? 'selected' : '' }}>Railing</option>
                            <option value="pintu" {{ old('project_type') == 'pintu' ? 'selected' : '' }}>Pintu</option>
                            <option value="teralis" {{ old('project_type') == 'teralis' ? 'selected' : '' }}>Teralis</option>
                            <option value="lainnya" {{ old('project_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('project_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="material_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Material <span class="text-red-500">*</span>
                        </label>
                        <select name="material_type" 
                                id="material_type" 
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('material_type') border-red-300 @enderror">
                            <option value="">Pilih Material</option>
                            <option value="hollow" {{ old('material_type') == 'hollow' ? 'selected' : '' }}>Hollow</option>
                            <option value="besi" {{ old('material_type') == 'besi' ? 'selected' : '' }}>Besi</option>
                            <option value="stainless" {{ old('material_type') == 'stainless' ? 'selected' : '' }}>Stainless Steel</option>
                            <option value="aluminium" {{ old('material_type') == 'aluminium' ? 'selected' : '' }}>Aluminium</option>
                        </select>
                        @error('material_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dimensi</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label for="length" class="block text-xs text-gray-600 mb-1">Panjang (m)</label>
                                <input type="number" 
                                       name="length" 
                                       id="length" 
                                       step="0.01"
                                       min="0"
                                       value="{{ old('length') }}"
                                       placeholder="0.00"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="width" class="block text-xs text-gray-600 mb-1">Lebar (m)</label>
                                <input type="number" 
                                       name="width" 
                                       id="width" 
                                       step="0.01"
                                       min="0"
                                       value="{{ old('width') }}"
                                       placeholder="0.00"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="height" class="block text-xs text-gray-600 mb-1">Tinggi (m)</label>
                                <input type="number" 
                                       name="height" 
                                       id="height" 
                                       step="0.01"
                                       min="0"
                                       value="{{ old('height') }}"
                                       placeholder="0.00"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="thickness" class="block text-xs text-gray-600 mb-1">Ketebalan (mm)</label>
                                <input type="number" 
                                       name="thickness" 
                                       id="thickness" 
                                       step="0.1"
                                       min="0"
                                       value="{{ old('thickness') }}"
                                       placeholder="0.0"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Proyek
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3"
                                  placeholder="Detail spesifikasi dan catatan proyek..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing & Status -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Harga & Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="estimated_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Estimasi (Rp)
                        </label>
                        <input type="number" 
                               name="estimated_price" 
                               id="estimated_price" 
                               step="1000"
                               min="0"
                               value="{{ old('estimated_price') }}"
                               placeholder="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="actual_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Aktual (Rp)
                        </label>
                        <input type="number" 
                               name="actual_price" 
                               id="actual_price" 
                               step="1000"
                               min="0"
                               value="{{ old('actual_price') }}"
                               placeholder="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                id="status" 
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="pending_dp" {{ old('status') == 'pending_dp' ? 'selected' : '' }}>Menunggu DP</option>
                            <option value="dp_pending_confirm" {{ old('status') == 'dp_pending_confirm' ? 'selected' : '' }}>DP Menunggu Konfirmasi</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                            <option value="ready_for_pickup" {{ old('status') == 'ready_for_pickup' ? 'selected' : '' }}>Siap Diambil</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div>
                        <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Pesanan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="order_date" 
                               id="order_date" 
                               value="{{ old('order_date', date('Y-m-d')) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="completion_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai
                        </label>
                        <input type="date" 
                               name="completion_date" 
                               id="completion_date" 
                               value="{{ old('completion_date') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="3"
                                  placeholder="Catatan tambahan atau informasi penting..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.orders.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Pesanan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
