@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Daftar Booking Jadwal Survei') }}</span>
                    <a href="{{ route('survey-booking.create') }}" class="btn btn-primary btn-sm">{{ __('Tambah Booking') }}</a>
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Produk</th>
                                    <th>Tanggal Survei</th>
                                    <th>Waktu Survei</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>{{ $booking->name }}</td>
                                        <td>{{ $booking->email }}</td>
                                        <td>{{ $booking->phone }}</td>
                                        <td>{{ $booking->product->name }}</td>
                                        <td>{{ $booking->survey_date->format('d-m-Y') }}</td>
                                        <td>{{ $booking->survey_time->format('H:i') }}</td>
                                        <td>
                                            @if ($booking->status == 'pending')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif ($booking->status == 'confirmed')
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                            @elseif ($booking->status == 'completed')
                                                <span class="badge bg-info">Selesai</span>
                                            @elseif ($booking->status == 'cancelled')
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('survey-booking.show', $booking->id) }}" class="btn btn-info btn-sm">Detail</a>
                                                <a href="{{ route('survey-booking.edit', $booking->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                                <form action="{{ route('survey-booking.destroy', $booking->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus booking ini?')">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada data booking</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection