Berikut ini brok ✅
📌 **Summary App – Versi Lengkap** untuk proyek **Website Estimasi & Booking Bengkel Las Azkal Jaya Las**, sudah menggabungkan seluruh alur sistem, fitur desain otomatis, integrasi ML, dan dashboard admin secara menyeluruh 👇

---

## 🧠 **Summary App – Sistem Estimasi Harga & Booking Bengkel Las Azkal Jaya Las**

### 🌐 1️⃣ **Gambaran Umum**

Website Bengkel **Azkal Jaya Las** merupakan platform berbasis web yang dibangun menggunakan **Laravel** sebagai backend utama dan **FastAPI (Python)** sebagai microservice Machine Learning.
Database utama menggunakan **MySQL**.
Tujuan sistem ini adalah untuk:

-   Memberikan **estimasi harga produk dan jasa bengkel las** secara cepat dan akurat.
-   Menyediakan **alur booking survei lokasi** yang mudah dan terstruktur.
-   Memungkinkan **admin untuk memperbarui dataset dan melakukan retraining model secara real-time**.
-   Menampilkan **contoh desain otomatis** sesuai input user agar pengguna memiliki gambaran visual yang lebih jelas.

---

### 🧩 2️⃣ **Alur Input User**

User (customer) yang ingin mengetahui estimasi biaya produk seperti pagar, kanopi, pintu gerbang, atau teralis, akan diarahkan ke halaman **Form Estimasi Harga**.
Form ini berisi input berikut:

| Kategori             | Input User                                    | Keterangan                                                                    |
| -------------------- | --------------------------------------------- | ----------------------------------------------------------------------------- |
| **Produk**           | Pagar, Kanopi, Pintu Gerbang, Teralis         | Menentukan metode hitung otomatis (per m² atau per lubang).                   |
| **Jumlah Unit**      | Jumlah total komponen                         | Contoh: 2 pagar, 1 kanopi.                                                    |
| **Jumlah Lubang**    | Hanya untuk Teralis                           | Karena dihitung per lubang.                                                   |
| **Ukuran (m²)**      | Panjang × Tinggi area kerja                   | Wajib untuk produk per m².                                                    |
| **Jenis Material**   | Hollow, Plat, Besi Siku, Aluminium, Stainless | Menentukan harga dasar & koefisien.                                           |
| **Profile Size**     | Contoh: 40x40, 40x60                          | Diisi jika material mendukung profil.                                         |
| **Ketebalan (mm)**   | Contoh: 0.8, 1.0, 1.2                         | Mempengaruhi biaya.                                                           |
| **Finishing**        | Tanpa Cat, Cat Duco, Powder Coating           | Tambahan biaya finishing.                                                     |
| **Kerumitan Desain** | Sederhana, Menengah, Rumit                    | Menambah koefisien pengerjaan **dan memicu tampilan contoh desain otomatis**. |

---

### 🖼️ 3️⃣ **✨ Fitur Tambahan – Contoh Desain Otomatis**

Saat user memilih **kerumitan desain**, sistem akan secara otomatis menampilkan **galeri contoh desain** yang sesuai dengan:

-   ✅ Jenis Produk (misalnya pagar vs kanopi)
-   ✅ Jenis Material (Hollow, Stainless, dll)
-   ✅ Tingkat Kerumitan (Sederhana → desain polos, Rumit → ornamental atau kombinasi)

Contoh:

-   Produk: _Pagar_, Material: _Hollow_, Kerumitan: _Menengah_ → muncul desain pagar minimalis dengan pola geometrik sedang.
-   Produk: _Teralis_, Material: _Besi Siku_, Kerumitan: _Rumit_ → muncul desain teralis ornamental dengan lengkungan.

Gambar desain ini dapat dikelola melalui:

-   📂 Folder `public/design_samples/` dengan struktur terorganisir.
-   🧰 Tabel `design_samples` di database untuk kategorisasi & filtering.
-   Admin bisa menambah atau mengganti gambar via dashboard.

---

### 📤 4️⃣ **Output Sistem ke User**

Setelah user mengisi form dan mengirimkan data, sistem akan menampilkan hasil:

| Output                        | Keterangan                                                      |
| ----------------------------- | --------------------------------------------------------------- |
| **Estimasi Harga Akhir (Rp)** | Nilai hasil perhitungan dan/atau prediksi ML                    |
| **Detail Perhitungan**        | Rincian dasar harga, ketebalan, finishing, dan koefisien desain |
| **Metode Hitung**             | Menunjukkan apakah per m² atau per lubang                       |
| **Galeri Contoh Desain**      | Gambar desain sesuai produk, material, dan kerumitan            |
| **Tombol Booking Survei**     | Arahkan ke form booking survei lokasi                           |

---

### 📝 5️⃣ **Alur Booking Survei**

Jika user ingin melanjutkan ke survei:

1. Klik **Booking Survei** → diarahkan ke halaman login/signup.
2. Setelah login, user mengisi **form jadwal survei** (tanggal, alamat, catatan tambahan).
3. Data booking otomatis tersimpan di database dan **notifikasi WhatsApp** dikirim ke pemilik bengkel.
4. Admin dapat mengelola semua booking melalui dashboard.

---

### 🧑‍💼 6️⃣ **Dashboard Admin**

Admin memiliki akses ke dashboard Laravel untuk mengelola:

-   📅 **Booking Survei** → Melihat daftar booking, mengubah status (Menunggu, Diproses, Selesai), dan menentukan surveyor.
-   🧾 **Pencatatan Transaksi Nyata** → Input ukuran aktual dan harga deal hasil survei.
-   📊 **Riwayat Transaksi** → Menjadi **dataset pelatihan model** berikutnya.
-   📂 **Export Dataset** → Download Excel/CSV untuk keperluan retraining manual.
-   📝 **Penambahan Dataset Realtime** → Admin dapat menambahkan data hasil transaksi langsung melalui dashboard tanpa perlu proses batch terpisah.
-   🖼️ **Manajemen Contoh Desain** → Upload, edit, hapus, dan kategorisasi desain untuk fitur galeri otomatis.

---

### 🤖 7️⃣ **Machine Learning & Realtime Retraining**

Model **Random Forest** digunakan untuk memperhalus estimasi harga berdasarkan data transaksi aktual.

-   Setiap estimasi & transaksi baru → tersimpan otomatis ke database.
-   Data tersebut dapat langsung digunakan untuk:

    -   🔁 **Retraining Otomatis** secara periodik (misalnya mingguan)
    -   🧠 **Retraining Manual** melalui dashboard admin

Dengan ini, sistem akan terus **belajar dan menyesuaikan diri** terhadap perubahan harga bahan, tren desain, dan kebiasaan pelanggan.

---

### 🚀 8️⃣ **Deployment**

Kedua komponen dideploy menggunakan **Railway**:

| Komponen               | Platform | Fungsi                                                         |
| ---------------------- | -------- | -------------------------------------------------------------- |
| **Laravel App**        | Railway  | Backend utama, frontend Blade, dashboard admin, booking system |
| **FastAPI ML Service** | Railway  | REST API untuk prediksi dan retraining model                   |

-   Keduanya dihubungkan via HTTPS endpoint dan environment variable.
-   Dataset & model `.pkl` disimpan di persistent storage (Railway Volume) atau cloud bucket.
-   Notifikasi WhatsApp menggunakan Twilio API (atau alternatif gateway lokal).

---

### 📌 9️⃣ **Kesimpulan**

-   ✅ User cukup isi form → sistem langsung menghitung & menampilkan estimasi harga lengkap dengan contoh desain.
-   📸 Fitur desain otomatis meningkatkan user experience dan membantu user memahami kerumitan proyek.
-   🧠 Model Random Forest memperhalus prediksi berdasarkan data historis.
-   👨‍💻 Admin dapat mengelola booking, transaksi, dan dataset secara real-time.
-   📈 Sistem adaptif dan siap dikembangkan ke arah otomasi retraining & personalisasi desain.

---

📌 **Batasan & Larangan AI Agent dalam Proses Development Proyek Azkal Jaya Las**

---

## 🚫 **Batasan & Larangan untuk AI Agent (Development Rule)**

### 1️⃣ **Batasan Framework & Teknologi**

-   ❌ **AI Agent tidak boleh menambahkan framework, library, atau dependency baru** selain yang telah ditentukan dalam stack berikut:

    -   **Backend:** Laravel (PHP)
    -   **ML Service:** FastAPI (Python)
    -   **Database:** MySQL
    -   **Frontend:** Blade Template + Tailwind

-   ❌ Tidak boleh mengganti framework utama dengan alternatif lain (misalnya mengganti Laravel dengan CodeIgniter, atau FastAPI dengan Flask tanpa alasan).
-   ✅ Jika ada kebutuhan library tambahan (misalnya package Python pendukung ML), harus **ditambahkan secara terstruktur di `requirements.txt` atau `composer.json`**, bukan asal tempel di kode.

---

### 2️⃣ **Batasan Struktur Folder & File**

-   ❌ **Tidak boleh membuat folder atau file duplikat** yang fungsinya sama, misalnya:

    -   `app/Http/Controllers/UserController.php` dan `app/Http/Controllers/UsersController.php` padahal isinya mirip.
    -   Dua folder `api/` di lokasi berbeda.

-   ❌ **Tidak boleh membuat file “sampah” atau dummy** seperti:

    -   `test.py`, `newfile.php`, `sample1.blade.php` yang tidak masuk dalam alur kerja proyek.
    -   Folder random seperti `temp/`, `backup/`, `test_folder/` di root project.

-   ✅ Struktur project harus mengikuti hierarki berikut:

    -   **Laravel:** Struktur default Laravel + folder tambahan terorganisir (misalnya `resources/views/admin`, `app/Services/ML`)
    -   **FastAPI:** Struktur modular minimal `main.py`, `model/`, `routes/`, `preprocess/`
    -   Tidak ada folder/fungsi ganda yang membingungkan.

---

### 3️⃣ **Batasan Pembuatan File Otomatis**

-   ❌ AI Agent **tidak boleh membuat file yang tidak akan digunakan dalam production** (misalnya file tes acak, contoh migration kosong, atau helper tidak dipakai).
-   ❌ Tidak boleh membuat file dengan nama tidak jelas (misal `script1.py`, `new.blade.php`) yang tidak sesuai konvensi.
-   ✅ Semua file harus:

    -   Punya **tujuan jelas**
    -   Diletakkan di **struktur folder yang benar**
    -   Mengikuti **penamaan standar Laravel / FastAPI** (snake_case untuk Python, PascalCase untuk Controller PHP, dll).

---

### 4️⃣ **Batasan Routing & Endpoint**

-   ❌ AI Agent tidak boleh membuat route atau endpoint yang **tidak masuk ke dalam flow aplikasi**, seperti route testing yang tidak dihapus (`/test`, `/demo`, dll).
-   ❌ Tidak boleh menambahkan endpoint baru tanpa dokumentasi dan pencatatan di file rencana arsitektur/API.
-   ✅ Setiap endpoint baru harus:

    -   Sesuai dengan alur sistem (estimasi harga, retraining, booking, dll).
    -   Tercatat di dokumentasi internal (misal `docs/api_routes.md`).

---

### 5️⃣ **Batasan Penamaan & Standar Kode**

-   ❌ AI Agent tidak boleh memakai nama file, variabel, atau function yang tidak konsisten atau “acak-acakan”.
    Contoh yang dilarang: `newController.php`, `data123.py`, `untitled.py`.
-   ✅ Gunakan **konvensi yang seragam**:

    -   PHP → PSR standard (Controller PascalCase, route snake_case).
    -   Python → snake_case untuk function, PascalCase untuk class.
    -   Blade → nama file sesuai fitur (`estimate_form.blade.php`, `booking_dashboard.blade.php`).

---

### 6️⃣ **Batasan Auto-Generate Komponen**

-   ❌ AI Agent **tidak boleh auto-generate CRUD penuh** untuk semua tabel tanpa filter — hanya generate jika diminta secara spesifik.
-   ❌ Tidak boleh membuat model/migration dummy untuk “contoh” yang tidak dipakai.
-   ✅ Kalau butuh generator (misalnya `php artisan make:model` atau `fastapi codegen`), hasilnya harus:

    -   Diperiksa ulang,
    -   Dihapus file dummy yang tidak dipakai,
    -   Disesuaikan dengan naming dan struktur proyek.

---

### 7️⃣ **Batasan Dokumentasi**

-   ❌ AI Agent tidak boleh membuat dokumentasi yang tidak sinkron dengan struktur real project.
-   ❌ Tidak boleh auto-generate dokumentasi tanpa update isi yang relevan.
-   ✅ Semua dokumentasi (API, struktur folder, arsitektur) harus mengacu ke struktur yang **benar-benar ada** di repositori.

---

### 8️⃣ **Batasan Dependency & File Konfigurasi**

-   ❌ AI Agent tidak boleh mengubah file konfigurasi inti seperti `.env.example`, `composer.json`, `package.json`, `requirements.txt`, `.env`, kecuali menambahkan dependency **yang sudah disetujui**.
-   ❌ Tidak boleh menambahkan konfigurasi service baru (Docker, CI/CD, dsb) tanpa catatan.
-   ✅ Semua perubahan pada file konfigurasi utama harus **terdokumentasi dan terstruktur**.

---

### 9️⃣ **Batasan Intervensi Antar Proyek**

-   ❌ AI Agent tidak boleh mencampur struktur Laravel dan FastAPI dalam satu repo.
-   ✅ Laravel dan FastAPI harus tetap **terpisah sebagai 2 repo atau 2 folder mandiri**:

    -   `laravel-app/` → backend utama
    -   `fastapi-ml/` → REST ML service

-   Keduanya hanya berkomunikasi lewat **HTTP API** yang jelas, bukan file sharing langsung.

---
