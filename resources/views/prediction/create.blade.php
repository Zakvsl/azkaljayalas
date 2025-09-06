<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pesanan - Azkal Jaya Las</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .hero-section {
            background-color: #198754;
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border: none;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            font-weight: bold;
        }
        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }
        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
        }
        .form-label {
            font-weight: 500;
        }
        .loading {
            display: none;
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <h1>Buat Pesanan</h1>
            <p class="lead">Isi formulir di bawah untuk membuat pesanan konstruksi Anda</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clipboard-list me-2"></i>Formulir Pesanan
                    </div>
                    <div class="card-body">
                        <form id="orderForm">
                            <h5 class="mb-3">Informasi Produk</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="product_id" class="form-label">Jenis Produk</label>
                                    <select class="form-select" id="product_id" name="product_id" required>
                                        <option value="" selected disabled>Pilih Jenis Produk</option>
                                        <option value="1">Kanopi</option>
                                        <option value="2">Pagar</option>
                                        <option value="3">Teralis</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="material_id" class="form-label">Jenis Material</label>
                                    <select class="form-select" id="material_id" name="material_id" required>
                                        <option value="" selected disabled>Pilih Jenis Material</option>
                                        <option value="1">Besi Standar</option>
                                        <option value="2">Besi Premium</option>
                                        <option value="3">Stainless Steel</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="finishing_id" class="form-label">Jenis Finishing</label>
                                    <select class="form-select" id="finishing_id" name="finishing_id" required>
                                        <option value="" selected disabled>Pilih Jenis Finishing</option>
                                        <option value="1">Cat Dasar</option>
                                        <option value="2">Cat Premium</option>
                                        <option value="3">Powder Coating</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="kerumitan_id" class="form-label">Tingkat Kerumitan</label>
                                    <select class="form-select" id="kerumitan_id" name="kerumitan_id" required>
                                        <option value="" selected disabled>Pilih Tingkat Kerumitan</option>
                                        <option value="1">Sederhana</option>
                                        <option value="2">Menengah</option>
                                        <option value="3">Kompleks</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="ketebalan_id" class="form-label">Ketebalan Material</label>
                                    <select class="form-select" id="ketebalan_id" name="ketebalan_id" required>
                                        <option value="" selected disabled>Pilih Ketebalan Material</option>
                                        <option value="1">Tipis</option>
                                        <option value="2">Sedang</option>
                                        <option value="3">Tebal</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label">Jumlah Unit</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="width" class="form-label">Lebar (meter)</label>
                                    <input type="number" step="0.01" class="form-control" id="width" name="width" min="0.1" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="height" class="form-label">Tinggi (meter)</label>
                                    <input type="number" step="0.01" class="form-control" id="height" name="height" min="0.1" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="length" class="form-label">Panjang (meter, opsional)</label>
                                    <input type="number" step="0.01" class="form-control" id="length" name="length" min="0">
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h5 class="mb-3">Informasi Pelanggan</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="survey_date" class="form-label">Tanggal Survei</label>
                                    <input type="date" class="form-control" id="survey_date" name="survey_date" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Catatan Tambahan (opsional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">
                                    <span class="normal-state">Kirim Pesanan</span>
                                    <span class="loading">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Mengirim...
                                    </span>
                                </button>
                            </div>
                        </form>
                        
                        <div id="successMessage" class="alert alert-success mt-4" style="display: none;">
                            <h4><i class="fas fa-check-circle me-2"></i>Pesanan Berhasil Dibuat!</h4>
                            <p>Tim kami akan menghubungi Anda segera untuk konfirmasi jadwal survei. Terima kasih telah mempercayai Azkal Jaya Las.</p>
                            <div class="d-grid gap-2 mt-3">
                                <a href="/prediction" class="btn btn-outline-success">Kembali ke Kalkulator Harga</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i>Informasi Pesanan
                    </div>
                    <div class="card-body">
                        <h5>Proses Pemesanan</h5>
                        <ol>
                            <li>Isi formulir pesanan dengan lengkap</li>
                            <li>Tim kami akan menghubungi Anda untuk konfirmasi</li>
                            <li>Survei lokasi pada tanggal yang ditentukan</li>
                            <li>Penawaran harga final setelah survei</li>
                            <li>Pembayaran uang muka untuk memulai pengerjaan</li>
                            <li>Pengerjaan dan pemasangan</li>
                            <li>Pelunasan setelah pemasangan selesai</li>
                        </ol>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Penting:</strong> Harga final akan ditentukan setelah survei lokasi.
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <a href="/prediction" class="btn btn-outline-secondary">Kembali ke Kalkulator</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Set minimum date for survey to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowFormatted = tomorrow.toISOString().split('T')[0];
            $('#survey_date').attr('min', tomorrowFormatted);
            
            $('#orderForm').on('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                $('.normal-state').hide();
                $('.loading').show();
                
                // Get form data
                const formData = $(this).serialize();
                
                // Simulate AJAX request (in a real app, this would send data to the server)
                setTimeout(function() {
                    // Hide loading state
                    $('.normal-state').show();
                    $('.loading').hide();
                    
                    // Hide form and show success message
                    $('#orderForm').hide();
                    $('#successMessage').slideDown();
                }, 1500);
                
                // In a real implementation, you would use AJAX to send the data to the server
                // $.ajax({
                //     url: '{{ route("prediction.store") }}',
                //     type: 'POST',
                //     data: formData,
                //     headers: {
                //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                //     },
                //     success: function(response) {
                //         // Hide loading state
                //         $('.normal-state').show();
                //         $('.loading').hide();
                //         
                //         if (response.success) {
                //             // Hide form and show success message
                //             $('#orderForm').hide();
                //             $('#successMessage').slideDown();
                //         } else {
                //             alert('Error: ' + response.message);
                //         }
                //     },
                //     error: function(xhr) {
                //         // Hide loading state
                //         $('.normal-state').show();
                //         $('.loading').hide();
                //         
                //         // Show error message
                //         let errorMessage = 'Terjadi kesalahan saat memproses permintaan.';
                //         
                //         if (xhr.responseJSON && xhr.responseJSON.errors) {
                //             errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                //         } else if (xhr.responseJSON && xhr.responseJSON.message) {
                //             errorMessage = xhr.responseJSON.message;
                //         }
                //         
                //         alert('Error: ' + errorMessage);
                //     }
                // });
            });
        });
    </script>
</body>
</html>