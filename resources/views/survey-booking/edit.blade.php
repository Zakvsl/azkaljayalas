@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Edit Booking Jadwal Survei') }}</span>
                    <a href="{{ route('survey-booking.index') }}" class="btn btn-secondary btn-sm">{{ __('Kembali') }}</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('survey-booking.update', $booking->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nama') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $booking->name) }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $booking->email) }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Nomor Telepon') }}</label>

                            <div class="col-md-6">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $booking->phone) }}" required autocomplete="phone">

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Alamat') }}</label>

                            <div class="col-md-6">
                                <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" required>{{ old('address', $booking->address) }}</textarea>

                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="product_id" class="col-md-4 col-form-label text-md-right">{{ __('Produk') }}</label>

                            <div class="col-md-6">
                                <select id="product_id" class="form-control @error('product_id') is-invalid @enderror" name="product_id" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id', $booking->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>

                                @error('product_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="material_id" class="col-md-4 col-form-label text-md-right">{{ __('Material') }}</label>

                            <div class="col-md-6">
                                <select id="material_id" class="form-control @error('material_id') is-invalid @enderror" name="material_id" required>
                                    <option value="">Pilih Material</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}" {{ old('material_id', $booking->material_id) == $material->id ? 'selected' : '' }}>{{ $material->name }}</option>
                                    @endforeach
                                </select>

                                @error('material_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="finishing_id" class="col-md-4 col-form-label text-md-right">{{ __('Finishing') }}</label>

                            <div class="col-md-6">
                                <select id="finishing_id" class="form-control @error('finishing_id') is-invalid @enderror" name="finishing_id" required>
                                    <option value="">Pilih Finishing</option>
                                    @foreach($finishings as $finishing)
                                        <option value="{{ $finishing->id }}" {{ old('finishing_id', $booking->finishing_id) == $finishing->id ? 'selected' : '' }}>{{ $finishing->name }}</option>
                                    @endforeach
                                </select>

                                @error('finishing_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="kerumitan_id" class="col-md-4 col-form-label text-md-right">{{ __('Kerumitan') }}</label>

                            <div class="col-md-6">
                                <select id="kerumitan_id" class="form-control @error('kerumitan_id') is-invalid @enderror" name="kerumitan_id" required>
                                    <option value="">Pilih Kerumitan</option>
                                    @foreach($kerumitans as $kerumitan)
                                        <option value="{{ $kerumitan->id }}" {{ old('kerumitan_id', $booking->kerumitan_id) == $kerumitan->id ? 'selected' : '' }}>{{ $kerumitan->name }}</option>
                                    @endforeach
                                </select>

                                @error('kerumitan_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="ketebalan_id" class="col-md-4 col-form-label text-md-right">{{ __('Ketebalan') }}</label>

                            <div class="col-md-6">
                                <select id="ketebalan_id" class="form-control @error('ketebalan_id') is-invalid @enderror" name="ketebalan_id" required>
                                    <option value="">Pilih Ketebalan</option>
                                    @foreach($ketebalans as $ketebalan)
                                        <option value="{{ $ketebalan->id }}" {{ old('ketebalan_id', $booking->ketebalan_id) == $ketebalan->id ? 'selected' : '' }}>{{ $ketebalan->name }}</option>
                                    @endforeach
                                </select>

                                @error('ketebalan_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="width" class="col-md-4 col-form-label text-md-right">{{ __('Lebar (cm)') }}</label>

                            <div class="col-md-6">
                                <input id="width" type="number" step="0.01" class="form-control @error('width') is-invalid @enderror" name="width" value="{{ old('width', $booking->width) }}" required>

                                @error('width')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="height" class="col-md-4 col-form-label text-md-right">{{ __('Tinggi (cm)') }}</label>

                            <div class="col-md-6">
                                <input id="height" type="number" step="0.01" class="form-control @error('height') is-invalid @enderror" name="height" value="{{ old('height', $booking->height) }}" required>

                                @error('height')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="length" class="col-md-4 col-form-label text-md-right">{{ __('Panjang (cm)') }}</label>

                            <div class="col-md-6">
                                <input id="length" type="number" step="0.01" class="form-control @error('length') is-invalid @enderror" name="length" value="{{ old('length', $booking->length) }}" required>

                                @error('length')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="quantity" class="col-md-4 col-form-label text-md-right">{{ __('Jumlah') }}</label>

                            <div class="col-md-6">
                                <input id="quantity" type="number" class="form-control @error('quantity') is-invalid @enderror" name="quantity" value="{{ old('quantity', $booking->quantity) }}" required>

                                @error('quantity')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="survey_date" class="col-md-4 col-form-label text-md-right">{{ __('Tanggal Survei') }}</label>

                            <div class="col-md-6">
                                <input id="survey_date" type="date" class="form-control @error('survey_date') is-invalid @enderror" name="survey_date" value="{{ old('survey_date', $booking->survey_date->format('Y-m-d')) }}" required>

                                @error('survey_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="survey_time" class="col-md-4 col-form-label text-md-right">{{ __('Waktu Survei') }}</label>

                            <div class="col-md-6">
                                <input id="survey_time" type="time" class="form-control @error('survey_time') is-invalid @enderror" name="survey_time" value="{{ old('survey_time', $booking->survey_time->format('H:i')) }}" required>

                                @error('survey_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="notes" class="col-md-4 col-form-label text-md-right">{{ __('Catatan') }}</label>

                            <div class="col-md-6">
                                <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes">{{ old('notes', $booking->notes) }}</textarea>

                                @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>

                            <div class="col-md-6">
                                <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" required>
                                    <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                                    <option value="completed" {{ old('status', $booking->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>

                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Simpan Perubahan') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection