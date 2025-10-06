# Machine Learning - Price Prediction System

## Deskripsi

Sistem prediksi harga menggunakan Random Forest Regressor untuk memprediksi harga proyek bengkel las berdasarkan berbagai parameter.

## Setup

### 1. Install Python Dependencies

```bash
cd ml
pip install -r requirements.txt
```

### 2. Konfigurasi Python Path

Tambahkan ke file `.env`:

```
PYTHON_PATH=python
```

Atau jika menggunakan virtual environment:

```
PYTHON_PATH=/path/to/venv/bin/python
```

### 3. Persiapan Dataset

Pastikan file dataset berada di root project dengan nama:
`dataset_transaksi_bengkel_las_130.xlsx`

Format kolom yang dibutuhkan:

-   `jenis_proyek`: Tipe proyek (pagar, kanopi, railing, dll)
-   `panjang`: Panjang dalam meter
-   `lebar`: Lebar dalam meter
-   `tinggi`: Tinggi dalam meter
-   `material`: Jenis material yang digunakan
-   `kompleksitas`: Tingkat kompleksitas (rendah, sedang, tinggi)
-   `harga_total`: Harga total (target variable)

## Training Model

### Via Admin Panel

1. Login sebagai admin
2. Navigasi ke menu "ML Model Management"
3. Klik tombol "Train Model"
4. Tunggu proses training selesai
5. Lihat hasil metrics (MAE, R²)

### Via Command Line

```bash
cd ml
python price_prediction.py train ../dataset_transaksi_bengkel_las_130.xlsx
```

## Testing Prediction

### Via Admin Panel

1. Setelah model ditraining
2. Gunakan form "Test Prediksi"
3. Masukkan parameter proyek
4. Klik "Test Prediksi"
5. Lihat hasil estimasi harga

### Via Command Line

```bash
cd ml
python price_prediction.py predict '{"jenis_proyek":"pagar","panjang":10,"lebar":2,"tinggi":1.5,"material":"besi_hollow","kompleksitas":"sedang"}'
```

## Integrasi dengan Laravel

### Menggunakan MLPredictionService

```php
use App\Services\MLPredictionService;

$mlService = new MLPredictionService();

// Training model
$results = $mlService->trainModel('/path/to/dataset.xlsx');

// Prediksi harga
$features = [
    'jenis_proyek' => 'pagar',
    'panjang' => 10,
    'lebar' => 2,
    'tinggi' => 1.5,
    'material' => 'besi_hollow',
    'kompleksitas' => 'sedang'
];

$price = $mlService->predictPrice($features);
```

## Model Files

Setelah training, model akan disimpan di:

-   `ml/models/price_model.pkl` - Model Random Forest
-   `ml/models/label_encoders.pkl` - Encoder untuk categorical features

## Troubleshooting

### Python tidak ditemukan

-   Install Python 3.8 atau lebih baru
-   Tambahkan Python ke PATH
-   Update `PYTHON_PATH` di `.env`

### Module tidak ditemukan

```bash
pip install -r ml/requirements.txt
```

### Permission denied

Pastikan folder `ml/models/` memiliki permission write

### Dataset format error

Pastikan dataset memiliki semua kolom yang dibutuhkan dan format yang benar

## Metrics Evaluation

### MAE (Mean Absolute Error)

Rata-rata error absolut dari prediksi. Semakin kecil semakin baik.

### R² (R-squared)

Koefisien determinasi, mengukur seberapa baik model fit dengan data.

-   R² = 1: Perfect fit
-   R² = 0: Model tidak lebih baik dari mean
-   R² < 0: Model buruk

## Feature Importance

Setelah training, Anda bisa mengecek feature importance untuk mengetahui fitur mana yang paling berpengaruh terhadap prediksi harga.

## Updating Model

Model bisa di-retrain kapan saja dengan dataset yang diupdate untuk meningkatkan akurasi.
