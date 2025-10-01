@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
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
                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal Survei</label>
                <input type="date" 
                       name="tanggal" 
                       id="tanggal" 
                       value="{{ old('tanggal') }}"
                       required 
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="waktu" class="block text-sm font-medium text-gray-700">Waktu</label>
                <select name="waktu" 
                        id="waktu" 
                        required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="pagi" {{ old('waktu') == 'pagi' ? 'selected' : '' }}>Pagi (08.00 - 11.00)</option>
                    <option value="siang" {{ old('waktu') == 'siang' ? 'selected' : '' }}>Siang (13.00 - 16.00)</option>
                </select>
            </div>
            
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                <textarea name="alamat" 
                          id="alamat" 
                          rows="3" 
                          required 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('alamat') }}</textarea>
            </div>
            
            <div>
                <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan Tambahan (Opsional)</label>
                <textarea name="catatan" 
                          id="catatan" 
                          rows="2" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('catatan') }}</textarea>
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