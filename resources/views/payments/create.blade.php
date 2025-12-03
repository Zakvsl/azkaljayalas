@extends('layouts.app')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('payments.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Pembayaran
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-4">Upload Bukti Pembayaran</h1>
            <p class="text-gray-600 mt-2">Upload bukti transfer pembayaran Anda</p>
        </div>

        <!-- Payment Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">Detail Pembayaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-blue-700">Proyek</label>
                    <p class="text-blue-900 font-semibold">{{ $payment->surveyBooking->project_type }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-blue-700">Tipe Pembayaran</label>
                    <p class="text-blue-900 font-semibold">
                        @if($payment->payment_type === 'dp')
                            DP ({{ $payment->dp_percentage }}%)
                        @else
                            Pelunasan
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-blue-700">Jumlah yang Harus Dibayar</label>
                    <p class="text-2xl font-bold text-blue-900">
                        Rp {{ number_format($payment->payment_type === 'dp' ? $payment->dp_amount : $payment->remaining_amount, 0, ',', '.') }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-blue-700">Total Harga Proyek</label>
                    <p class="text-blue-900">Rp {{ number_format($payment->total_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form action="{{ route('payments.store', $payment) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Payment Proof -->
                <div class="mb-6">
                    <label for="payment_proof" class="block text-sm font-medium text-gray-700 mb-2">
                        Bukti Transfer <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                        id="payment_proof" 
                        name="payment_proof" 
                        accept="image/jpeg,image/png,image/jpg"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_proof') border-red-500 @enderror"
                        required>
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB</p>
                    @error('payment_proof')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Paid Amount -->
                <div class="mb-6">
                    <label for="paid_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah yang Ditransfer <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-2.5 text-gray-500">Rp</span>
                        <input type="number" 
                            id="paid_amount" 
                            name="paid_amount" 
                            value="{{ old('paid_amount', $payment->payment_type === 'dp' ? $payment->dp_amount : $payment->remaining_amount) }}"
                            class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('paid_amount') border-red-500 @enderror"
                            required>
                    </div>
                    @error('paid_amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div class="mb-6">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                        Metode Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="payment_method" 
                        name="payment_method"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_method') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Metode</option>
                        <option value="transfer_bca" {{ old('payment_method') === 'transfer_bca' ? 'selected' : '' }}>Transfer BCA</option>
                        <option value="transfer_mandiri" {{ old('payment_method') === 'transfer_mandiri' ? 'selected' : '' }}>Transfer Mandiri</option>
                        <option value="transfer_bri" {{ old('payment_method') === 'transfer_bri' ? 'selected' : '' }}>Transfer BRI</option>
                        <option value="transfer_bni" {{ old('payment_method') === 'transfer_bni' ? 'selected' : '' }}>Transfer BNI</option>
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                    </select>
                    @error('payment_method')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Notes -->
                <div class="mb-8">
                    <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea 
                        id="payment_notes" 
                        name="payment_notes" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Catatan tambahan tentang pembayaran ini">{{ old('payment_notes') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('payments.index') }}" class="text-gray-600 hover:text-gray-800">
                        Batal
                    </a>
                    <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition duration-200">
                        📤 Upload Bukti Pembayaran
                    </button>
                </div>
            </form>
        </div>

        <!-- Bank Info -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h4 class="font-semibold text-yellow-900 mb-2">ℹ️ Informasi Rekening</h4>
            <div class="text-sm text-yellow-800 space-y-1">
                <p><strong>BCA:</strong> 1234567890 a/n Azkal Jaya Las</p>
                <p><strong>Mandiri:</strong> 0987654321 a/n Azkal Jaya Las</p>
                <p class="mt-2 text-xs">Pastikan jumlah transfer sesuai dengan yang tertera di atas</p>
            </div>
        </div>
    </div>
</div>
@endsection
