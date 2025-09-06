<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konfirmasi Booking Jadwal Survei</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Konfirmasi Booking Jadwal Survei</h1>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $booking->name }}</strong>,</p>
            
            <p>Terima kasih telah melakukan booking jadwal survei di Azkal Jaya Las. Berikut adalah detail booking Anda:</p>
            
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
            </table>
            
            <p>Tim kami akan segera menghubungi Anda untuk mengkonfirmasi jadwal survei. Jika Anda memiliki pertanyaan, silakan hubungi kami di nomor telepon yang tertera di bawah.</p>
            
            <p>Terima kasih atas kepercayaan Anda pada Azkal Jaya Las.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Azkal Jaya Las. All rights reserved.</p>
            <p>Jl. Contoh No. 123, Kota, Indonesia</p>
            <p>Telepon: +62 123 4567 890 | Email: info@azkaljayalas.com</p>
        </div>
    </div>
</body>
</html>