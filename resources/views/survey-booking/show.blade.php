@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Detail Booking Jadwal Survei') }}</span>
                    <a href="{{ route('survey-booking.index') }}" class="btn btn-secondary btn-sm">{{ __('Kembali') }}</a>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">ID Booking:</div>
                        <div class="col-md-8">{{ $booking->id }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Nama:</div>
                        <div class="col-md-8">{{ $booking->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Email:</div>
                        <div class="col-md-8">{{ $booking->email }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Telepon:</div>
                        <div class="col-md-8">{{ $booking->phone }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Alamat:</div>
                        <div class="col-md-8">{{ $booking->address }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Produk:</div>
                        <div class="col-md-8">{{ $booking->product->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Material:</div>
                        <div class="col-md-8">{{ $booking->material->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Finishing:</div>
                        <div class="col-md-8">{{ $booking->finishing->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Kerumitan:</div>
                        <div class="col-md-8">{{ $booking->kerumitan->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Ketebalan:</div>
                        <div class="col-md-8">{{ $booking->ketebalan->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Dimensi (P x L x T):</div>
                        <div class="col-md-8">{{ $booking->length }} x {{ $booking->width }} x {{ $booking->height }} cm</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Jumlah:</div>
                        <div class="col-md-8">{{ $booking->quantity }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Tanggal Survei:</div>
                        <div class="col-md-8">{{ $booking->survey_date->format('d-m-Y') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Waktu Survei:</div>
                        <div class="col-md-8">{{ $booking->survey_time->format('H:i') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Catatan:</div>
                        <div class="col-md-8">{{ $booking->notes ?: 'Tidak ada catatan' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Status:</div>
                        <div class="col-md-8">
                            @if ($booking->status == 'pending')
                                <span class="badge bg-warning">Menunggu</span>
                            @elseif ($booking->status == 'confirmed')
                                <span class="badge bg-success">Dikonfirmasi</span>
                            @elseif ($booking->status == 'completed')
                                <span class="badge bg-info">Selesai</span>
                            @elseif ($booking->status == 'cancelled')
                                <span class="badge bg-danger">Dibatalkan</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Tanggal Dibuat:</div>
                        <div class="col-md-8">{{ $booking->created_at->format('d-m-Y H:i:s') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Terakhir Diperbarui:</div>
                        <div class="col-md-8">{{ $booking->updated_at->format('d-m-Y H:i:s') }}</div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 d-flex justify-content-between">
                            <a href="{{ route('survey-booking.edit', $booking->id) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('survey-booking.destroy', $booking->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus booking ini?')">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection