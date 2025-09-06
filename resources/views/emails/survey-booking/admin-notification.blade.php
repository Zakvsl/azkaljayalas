<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifikasi Booking Jadwal Survei Baru</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0d6efd;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0d6efd;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Notifikasi Booking Jadwal Survei Baru</h1>
        </div>
        
        <div class="content">
            <div class="alert">
                <p><strong>Perhatian!</strong> Ada booking jadwal survei baru yang memerlukan tindak lanjut.</p>
            </div>
            
            <p>Berikut adalah detail booking jadwal survei:</p>
            
            <table>
                <tr>
                    <th>ID Booking</th>
                    <td>{{ $booking->id }}</td>
                </tr>
                <tr>
                    <th>Nama</th>
                    <td>{{ $booking->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $booking->email }}</td>
                </tr>
                <tr>
                    <th>Telepon</th>
                    <td>{{ $booking->phone }}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{ $booking->address }}</td>
                </tr>
                <tr>
                    <th>Produk</th>
                    <td>{{ $booking->product->name }}</td>
                </tr>
                <tr>
                    <th>Material</th>
                    <td>{{ $booking->material->name }}</td>
                </tr>
                <tr>
                    <th>Finishing</th>
                    <td>{{ $booking->finishing->name }}</td>
                </tr>
                <tr>
                    <th>Kerumitan</th>
                    <td>{{ $booking->kerumitan->name }}</td>
                </tr>
                <tr>
                    <th>Ketebalan</th>
                    <td>{{ $booking->ketebalan->name }}</td>
                </tr>
                <tr>
                    <th>Dimensi (P x L x T)</th>
                    <td>{{ $booking->length }} x {{ $booking->width }} x {{ $booking->height }} cm</td>
                </tr>
                <tr>
                    <th>Jumlah</th>
                    <td>{{ $booking->quantity }}</td>
                </tr>
                <tr>
                    <th>Tanggal Survei</th>
                    <td>{{ $booking->survey_date->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <th>Waktu Survei</th>
                    <td>{{ $booking->survey_time->format('H:i') }}</td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td>{{ $booking->notes ?: 'Tidak ada catatan' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if ($booking->status == 'pending')
                            Menunggu
                        @elseif ($booking->status == 'confirmed')
                            Dikonfirmasi
                        @elseif ($booking->status == 'completed')
                            Selesai
                        @elseif ($booking->status == 'cancelled')
                            Dibatalkan
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Dibuat</th>
                    <td>{{ $booking->created_at->format('d-m-Y H:i:s') }}</td>
                </tr>
            </table>
            
            <p>Silakan segera tindak lanjuti booking ini dengan menghubungi pelanggan untuk mengkonfirmasi jadwal survei.</p>
            
            <a href="{{ route('survey-booking.show', $booking->id) }}" class="btn">Lihat Detail Booking</a>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon jangan membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Azkal Jaya Las. All rights reserved.</p>
        </div>
    </div>
</body>
</html>