<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Harga - Azkal Jaya Las</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .hero-section {
            background-color: #0d6efd;
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
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        .form-label {
            font-weight: 500;
        }
        .result-box {
            background-color: #e9ecef;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
        .price-result {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .loading {
            display: none;
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <h1>Prediksi Harga Konstruksi</h1>
            <p class="lead">Dapatkan estimasi harga untuk proyek konstruksi Anda dengan cepat dan akurat</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-calculator me-2"></i>Kalkulator Harga
                    </div>
                    <div class="card-body">
                        <form id="predictionForm">
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
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <span class="normal-state">Hitung Estimasi Harga</span>
                                    <span class="loading">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Menghitung...
                                    </span>
                                </button>
                            </div>
                        </form>
                        
                        <div id="resultContainer" class="result-box mt-4" style="display: none;">
                            <h4>Hasil Estimasi Harga</h4>
                            <div class="price-result mb-2" id="priceResult">Rp 0</div>
                            <p class="text-muted">*Estimasi harga ini hanya perkiraan. Untuk harga final, silakan hubungi tim kami.</p>
                            <div class="d-grid gap-2">
                                <a href="/prediction/create" class="btn btn-success">Buat Pesanan</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i>Informasi
                    </div>
                    <div class="card-body">
                        <h5>Tentang Kalkulator Harga</h5>
                        <p>Kalkulator ini memberikan estimasi harga berdasarkan parameter yang Anda masukkan. Harga aktual dapat bervariasi tergantung pada:</p>
                        <ul>
                            <li>Kondisi lokasi pemasangan</li>
                            <li>Ketersediaan material</li>
                            <li>Detail desain khusus</li>
                            <li>Biaya transportasi</li>
                        </ul>
                        <p>Untuk penawaran harga yang lebih akurat, silakan hubungi tim kami untuk survei lokasi.</p>
                        
                        <div class="d-grid gap-2 mt-4">
                            <a href="#" class="btn btn-outline-primary">Hubungi Kami</a>
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
            $('#predictionForm').on('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                $('.normal-state').hide();
                $('.loading').show();
                
                // Get form data
                const formData = $(this).serialize();
                
                // Send AJAX request
                $.ajax({
                    url: '{{ route("prediction.calculate") }}',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Hide loading state
                        $('.normal-state').show();
                        $('.loading').hide();
                        
                        if (response.success) {
                            // Format price
                            const formattedPrice = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: response.currency,
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(response.price);
                            
                            // Show result
                            $('#priceResult').text(formattedPrice);
                            $('#resultContainer').slideDown();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        // Hide loading state
                        $('.normal-state').show();
                        $('.loading').hide();
                        
                        // Show error message
                        let errorMessage = 'Terjadi kesalahan saat memproses permintaan.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        alert('Error: ' + errorMessage);
                    }
                });
            });
        });
    </script>
</body>
</html>