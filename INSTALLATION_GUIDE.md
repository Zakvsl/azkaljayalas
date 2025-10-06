# Panduan Instalasi Azkal Jaya Las

## Requirements

-   PHP >= 8.2
-   MySQL/MariaDB
-   Composer
-   Node.js & NPM
-   Python >= 3.8 (untuk Machine Learning)
-   XAMPP/WAMP atau web server lainnya

## Langkah-langkah Instalasi

### 1. Clone/Download Project

```bash
# Jika menggunakan Git
git clone <repository-url>
cd azkaljayalas2

# Atau extract file ZIP ke folder htdocs
```

### 2. Install Dependencies PHP

```bash
composer install
```

### 3. Install Dependencies JavaScript

```bash
npm install
npm run build
```

### 4. Setup Environment

```bash
# Copy file .env.example menjadi .env
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=azkaljayalas
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Buat Database

Buka phpMyAdmin atau MySQL client dan buat database:

```sql
CREATE DATABASE azkaljayalas;
```

### 7. Jalankan Migration

```bash
php artisan migrate
```

### 8. Setup Python untuk Machine Learning

#### Windows:

```bash
# Install Python dari python.org (minimal versi 3.8)
# Pastikan Python sudah ditambahkan ke PATH

# Install dependencies ML
cd ml
pip install -r requirements.txt
cd ..
```

#### Linux/Mac:

```bash
# Install Python3 dan pip
sudo apt-get install python3 python3-pip  # Ubuntu/Debian
# atau
brew install python3  # MacOS

# Install dependencies ML
cd ml
pip3 install -r requirements.txt
cd ..
```

### 9. Konfigurasi Python Path

Edit file `.env` dan tambahkan:

```env
# Windows
PYTHON_PATH=python

# Linux/Mac (jika menggunakan python3)
PYTHON_PATH=python3

# Atau jika menggunakan virtual environment
PYTHON_PATH=/path/to/venv/bin/python
```

### 10. Buat Admin User

```bash
php artisan tinker
```

Kemudian jalankan di dalam tinker:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@azkaljayalas.com',
    'password' => Hash::make('admin123'),
    'phone_number' => '081234567890',
    'address' => 'Alamat Admin',
    'role' => 'admin'
]);

exit
```

### 11. Pastikan Dataset Tersedia

Pastikan file `dataset_transaksi_bengkel_las_130.xlsx` berada di root folder project.

### 12. Jalankan Development Server

```bash
php artisan serve
```

Aplikasi akan berjalan di: `http://localhost:8000`

### 13. Training Machine Learning Model

#### Cara 1: Via Admin Panel (Recommended)

1. Login sebagai admin di `http://localhost:8000/login`
    - Email: `admin@azkaljayalas.com`
    - Password: `admin123`
2. Navigasi ke menu "ML Model Management"
3. Klik tombol "Train Model"
4. Tunggu proses training selesai (beberapa menit)

#### Cara 2: Via Command Line

```bash
cd ml
python price_prediction.py train ../dataset_transaksi_bengkel_las_130.xlsx
```

## Verifikasi Instalasi

### 1. Test Homepage

Buka browser dan akses: `http://localhost:8000`

-   Homepage harus tampil dengan baik
-   Hero section dengan tombol CTA

### 2. Test Register & Login

1. Klik "Sign Up"
2. Isi form registrasi
3. Login dengan akun yang baru dibuat

### 3. Test Estimasi Harga (Tanpa Login)

1. Di homepage, klik "Estimasi Harga"
2. Isi form estimasi
3. Harus mendapat hasil prediksi harga

### 4. Test Booking Survei (Dengan Login)

1. Login terlebih dahulu
2. Klik "Jadwalkan Survei"
3. Isi form booking
4. Submit → harus berhasil

### 5. Test Admin Panel

1. Login sebagai admin
2. Akses dashboard admin
3. Test management booking survei
4. Test ML model management

## Troubleshooting

### Error: "SQLSTATE[HY000] [1045] Access denied"

**Solusi:** Periksa username dan password database di file `.env`

### Error: "Class 'X' not found"

**Solusi:** Jalankan `composer dump-autoload`

### Error: Python tidak ditemukan

**Solusi:**

-   Install Python dari python.org
-   Tambahkan Python ke PATH
-   Update `PYTHON_PATH` di `.env`

### Error: "Module 'sklearn' not found"

**Solusi:**

```bash
cd ml
pip install -r requirements.txt
```

### Error: Permission denied pada folder models

**Solusi (Windows):**

-   Klik kanan folder `ml/models`
-   Properties → Security → Edit
-   Berikan Full Control

**Solusi (Linux/Mac):**

```bash
chmod -R 777 ml/models
```

### Homepage tidak menampilkan CSS dengan baik

**Solusi:**

```bash
npm run build
# atau untuk development
npm run dev
```

### Error: "Base table or view already exists"

**Solusi:**

```bash
php artisan migrate:fresh
# PERHATIAN: Ini akan menghapus semua data!
```

## Testing Checklist

-   [ ] Homepage tampil dengan baik
-   [ ] Register user baru berhasil
-   [ ] Login berhasil
-   [ ] Estimasi harga tanpa login berhasil
-   [ ] Booking survei dengan login berhasil
-   [ ] Admin bisa login
-   [ ] Admin bisa lihat list booking
-   [ ] Admin bisa update status booking
-   [ ] ML Model berhasil di-training
-   [ ] Test prediction ML berhasil

## Catatan Penting

1. **Database**: Pastikan MySQL/MariaDB sudah running
2. **XAMPP**: Pastikan Apache dan MySQL sudah distart
3. **Python**: Versi minimal 3.8
4. **Dataset**: File dataset harus ada sebelum training model
5. **Admin Account**: Simpan baik-baik kredensial admin

## Kredensial Default

### Admin

-   Email: `admin@azkaljayalas.com`
-   Password: `admin123`

**PENTING:** Ganti password admin setelah instalasi!

## Production Deployment

Untuk deployment ke production server:

1. Set `APP_ENV=production` di `.env`
2. Set `APP_DEBUG=false`
3. Jalankan `php artisan optimize`
4. Jalankan `php artisan config:cache`
5. Jalankan `php artisan route:cache`
6. Setup proper web server (Apache/Nginx)
7. Setup SSL certificate
8. Setup backup database otomatis

## Support

Jika mengalami masalah:

1. Cek file `storage/logs/laravel.log`
2. Cek console browser (F12) untuk error JavaScript
3. Dokumentasi Laravel: https://laravel.com/docs
4. Dokumentasi scikit-learn: https://scikit-learn.org/

---

**Selamat menggunakan Azkal Jaya Las Web Application!**
