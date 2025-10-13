@extends('layouts.admin')

@section('title', 'Estimate Details')
@section('page-title', 'Estimate Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.estimates.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Estimates
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user text-blue-600 mr-2"></i>
                Customer Information
            </h3>
            <div class="space-y-3">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $estimate->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $estimate->user->email }}</dd>
                </div>
                @if($estimate->user->phone)
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $estimate->user->phone }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Request Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $estimate->created_at->format('F j, Y') }}</dd>
                    <dd class="text-xs text-gray-500">{{ $estimate->created_at->format('g:i A') }}</dd>
                </div>
            </div>
        </div>

        <!-- Project Details -->
        <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-clipboard-list text-green-600 mr-2"></i>
                Project Details
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Product Type</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $estimate->jenis_produk }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Material</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $estimate->jenis_material)) }}</dd>
                </div>
                @if($estimate->profile_size)
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Profile Size</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $estimate->profile_size }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Thickness</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $estimate->ketebalan_mm }} mm</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Quantity</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $estimate->jumlah_unit }} unit(s)</dd>
                </div>
                @if($estimate->ukuran_m2)
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Size</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $estimate->ukuran_m2 }} m²</dd>
                </div>
                @endif
                @if($estimate->jumlah_lubang)
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Number of Holes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $estimate->jumlah_lubang }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Finishing</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $estimate->finishing)) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase">Design Complexity</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($estimate->kerumitan_desain == 1)
                            <span class="text-green-600">Simple</span>
                        @elseif($estimate->kerumitan_desain == 2)
                            <span class="text-yellow-600">Medium</span>
                        @else
                            <span class="text-red-600">Complex</span>
                        @endif
                    </dd>
                </div>
            </div>
            
            @if($estimate->notes)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <dt class="text-xs font-medium text-gray-500 uppercase mb-2">Customer Notes</dt>
                <dd class="text-sm text-gray-900 whitespace-pre-wrap bg-gray-50 p-4 rounded">{{ $estimate->notes }}</dd>
            </div>
            @endif
        </div>
    </div>

    <!-- Pricing Information -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
            Pricing Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <dt class="text-xs font-medium text-blue-600 uppercase mb-1">Estimated Price</dt>
                <dd class="text-2xl font-bold text-blue-900">Rp {{ number_format($estimate->harga_akhir, 0, ',', '.') }}</dd>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <dt class="text-xs font-medium text-green-600 uppercase mb-1">Actual Price</dt>
                <dd class="text-2xl font-bold text-green-900">
                    @if($estimate->actual_price)
                        Rp {{ number_format($estimate->actual_price, 0, ',', '.') }}
                    @else
                        <span class="text-base text-gray-400">Not set</span>
                    @endif
                </dd>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <dt class="text-xs font-medium text-purple-600 uppercase mb-1">Status</dt>
                <dd class="mt-1">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                        @if($estimate->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($estimate->status === 'confirmed') bg-green-100 text-green-800
                        @elseif($estimate->status === 'completed') bg-blue-100 text-blue-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($estimate->status) }}
                    </span>
                </dd>
            </div>
        </div>
    </div>

    <!-- Update Status Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-edit text-purple-600 mr-2"></i>
            Update Estimate
        </h3>
        <form action="{{ route('admin.estimates.update', $estimate) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="pending" {{ $estimate->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $estimate->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="rejected" {{ $estimate->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ $estimate->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="actual_price" class="block text-sm font-medium text-gray-700 mb-2">Actual Price (Optional)</label>
                    <input type="number" name="actual_price" id="actual_price" step="0.01" 
                        value="{{ old('actual_price', $estimate->actual_price) }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        placeholder="Enter actual price">
                    @error('actual_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-wider hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                        <i class="fas fa-save mr-2"></i>
                        Update Estimate
                    </button>
                </div>
            </div>
            
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                <textarea name="notes" id="notes" rows="3"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    placeholder="Add any notes or comments...">{{ old('notes', $estimate->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </form>
    </div>

    <!-- Delete Button -->
    <div class="flex justify-end">
        <form action="{{ route('admin.estimates.destroy', $estimate) }}" method="POST" 
            onsubmit="return confirm('Are you sure you want to delete this estimate? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                <i class="fas fa-trash mr-2"></i>
                Delete Estimate
            </button>
        </form>
    </div>
</div>
@endsection