@extends('layouts.app')
<!-- Navbar -->
@include('components.navbar')
@section('content')
<div class="container mx-auto px-4 pt-20 pb-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Jadwalkan Survei</h2>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('survey.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="project_type" class="block text-sm font-medium text-gray-700">Jenis Proyek <span class="text-red-500">*</span></label>
                <select name="project_type" 
                        id="project_type" 
                        required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Jenis Proyek</option>
                    <option value="pagar" {{ old('project_type') == 'pagar' ? 'selected' : '' }}>Pagar</option>
                    <option value="kanopi" {{ old('project_type') == 'kanopi' ? 'selected' : '' }}>Kanopi</option>
                    <option value="railing" {{ old('project_type') == 'railing' ? 'selected' : '' }}>Railing</option>
                    <option value="pintu" {{ old('project_type') == 'pintu' ? 'selected' : '' }}>Pintu</option>
                    <option value="teralis" {{ old('project_type') == 'teralis' ? 'selected' : '' }}>Teralis</option>
                    <option value="lainnya" {{ old('project_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            
            <div>
                <label for="project_description" class="block text-sm font-medium text-gray-700">Deskripsi Proyek <span class="text-red-500">*</span></label>
                <textarea name="project_description" 
                          id="project_description" 
                          rows="3" 
                          required 
                          placeholder="Jelaskan detail proyek yang Anda inginkan..."
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('project_description') }}</textarea>
            </div>
            
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700">Lokasi Survei <span class="text-red-500">*</span></label>
                <textarea name="location" 
                          id="location" 
                          rows="2" 
                          required 
                          placeholder="Alamat lengkap lokasi survei..."
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('location') }}</textarea>
            </div>
            
            <div>
                <label for="preferred_date" class="block text-sm font-medium text-gray-700">Tanggal yang Diinginkan <span class="text-red-500">*</span></label>
                <input type="date" 
                       name="preferred_date" 
                       id="preferred_date" 
                       value="{{ old('preferred_date') }}"
                       required 
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" 
                          id="notes" 
                          rows="2" 
                          placeholder="Catatan atau permintaan khusus..."
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>
            
            <div>
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Jadwalkan Survei
                </button>
            </div>
        </form>
    </div>
</div>
@endsection