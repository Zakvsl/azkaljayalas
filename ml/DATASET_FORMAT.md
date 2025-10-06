# Dataset Format untuk Machine Learning

## Deskripsi

File ini menjelaskan format dataset yang dibutuhkan untuk training model prediksi harga bengkel las.

## Format File

-   **Nama File**: `dataset_transaksi_bengkel_las_130.xlsx`
-   **Format**: Excel (.xlsx) atau CSV (.csv)
-   **Lokasi**: Root folder project

## Struktur Kolom yang Dibutuhkan

### 1. jenis_proyek (string)

Jenis/tipe proyek yang dikerjakan.

**Nilai yang Valid:**

-   `pagar`
-   `kanopi`
-   `railing`
-   `pintu`
-   `teralis`
-   `lainnya`

**Contoh:**

```
pagar
kanopi
railing
```

### 2. panjang (numeric)

Dimensi panjang dalam satuan meter.

**Format:** Angka desimal (float)

**Range:** 0.1 - 100 meter

**Contoh:**

```
10.5
5.0
15.75
```

### 3. lebar (numeric)

Dimensi lebar dalam satuan meter.

**Format:** Angka desimal (float)

**Range:** 0.1 - 50 meter

**Contoh:**

```
2.0
1.5
3.25
```

### 4. tinggi (numeric)

Dimensi tinggi dalam satuan meter.

**Format:** Angka desimal (float)

**Range:** 0.1 - 20 meter

**Contoh:**

```
1.5
2.0
0.8
```

### 5. material (string)

Jenis material yang digunakan.

**Nilai yang Valid:**

-   `besi_hollow`
-   `stainless_steel`
-   `besi_beton`
-   `aluminium`
-   `besi_siku`
-   `besi_cnp`

**Contoh:**

```
besi_hollow
stainless_steel
aluminium
```

### 6. kompleksitas (string)

Tingkat kompleksitas atau kesulitan pengerjaan.

**Nilai yang Valid:**

-   `rendah` - Pekerjaan sederhana, desain standar
-   `sedang` - Pekerjaan dengan detail tambahan
-   `tinggi` - Pekerjaan rumit dengan banyak detail

**Contoh:**

```
rendah
sedang
tinggi
```

### 7. harga_total (numeric) - TARGET VARIABLE

Total harga proyek dalam Rupiah.

**Format:** Angka bulat (integer)

**Range:** 500,000 - 50,000,000

**Contoh:**

```
5000000
3500000
12000000
```

## Contoh Dataset (Excel/CSV)

| jenis_proyek | panjang | lebar | tinggi | material        | kompleksitas | harga_total |
| ------------ | ------- | ----- | ------ | --------------- | ------------ | ----------- |
| pagar        | 10.0    | 2.0   | 1.5    | besi_hollow     | sedang       | 5000000     |
| kanopi       | 5.0     | 3.0   | 0.5    | besi_hollow     | rendah       | 3500000     |
| railing      | 8.0     | 1.2   | 1.0    | stainless_steel | tinggi       | 12000000    |
| pintu        | 2.5     | 1.0   | 2.0    | besi_beton      | sedang       | 4000000     |
| teralis      | 3.0     | 1.5   | 1.8    | aluminium       | rendah       | 2500000     |

## Validasi Data

### Data Quality Checks

1. **No Missing Values**: Semua kolom harus terisi
2. **Correct Data Types**: Numeric untuk dimensi dan harga, string untuk kategori
3. **Valid Categories**: Kategori harus sesuai dengan list yang valid
4. **Logical Values**:
    - Dimensi harus positif (> 0)
    - Harga harus realistis
    - Konsistensi antara dimensi dan harga

### Minimum Dataset Size

-   **Minimal**: 50 sampel untuk training dasar
-   **Recommended**: 100+ sampel untuk hasil lebih baik
-   **Optimal**: 200+ sampel untuk akurasi tinggi

## Feature Engineering

Model akan otomatis membuat encoding untuk:

-   `jenis_proyek_encoded`
-   `material_encoded`
-   `kompleksitas_encoded`

Dan menggunakan features:

-   Dimensi (panjang, lebar, tinggi)
-   Encoded categorical variables

## Tips untuk Data Collection

### 1. Variasi Data

Pastikan dataset memiliki variasi:

-   Berbagai jenis proyek
-   Range dimensi yang beragam
-   Semua jenis material
-   Semua level kompleksitas

### 2. Data Balance

Usahakan jumlah sampel untuk setiap kategori seimbang:

```
pagar: 25-30 sampel
kanopi: 25-30 sampel
railing: 20-25 sampel
pintu: 15-20 sampel
teralis: 15-20 sampel
```

### 3. Outlier Handling

Hati-hati dengan outlier (nilai ekstrem):

-   Harga terlalu rendah atau tinggi
-   Dimensi tidak realistis
-   Kombinasi yang tidak masuk akal

### 4. Data Consistency

Pastikan konsistensi:

-   Satuan yang sama (meter untuk dimensi)
-   Format harga (tanpa koma atau titik)
-   Ejaan kategori yang konsisten

## Contoh Data Real

### Pagar Besi Hollow

```
panjang: 10m, lebar: 2m, tinggi: 1.5m
material: besi_hollow
kompleksitas: sedang
harga: 5,000,000
```

### Kanopi Minimalis

```
panjang: 6m, lebar: 3m, tinggi: 0.3m
material: besi_hollow
kompleksitas: rendah
harga: 4,000,000
```

### Railing Tangga Stainless

```
panjang: 8m, lebar: 1.2m, tinggi: 1m
material: stainless_steel
kompleksitas: tinggi
harga: 15,000,000
```

## Testing Dataset

Sebelum training, test dengan sample kecil (10-20 baris) untuk memastikan:

1. File bisa dibaca dengan benar
2. Semua kolom terdeteksi
3. Data types sesuai
4. Tidak ada error encoding

## Error Handling

### Common Errors

**Error: "Column not found"**

-   Pastikan semua kolom ada dan ejaan benar

**Error: "Invalid data type"**

-   Periksa format numeric (gunakan titik, bukan koma untuk desimal)
-   Contoh: `10.5` bukan `10,5`

**Error: "Encoding failed"**

-   Periksa kategori, pastikan sesuai list valid
-   Perhatikan huruf besar/kecil (case-sensitive)

## Updating Dataset

Untuk update model dengan data baru:

1. Tambahkan data baru ke file dataset
2. Re-train model melalui admin panel
3. Model lama akan di-overwrite
4. Test prediction dengan data baru

## Backup

Selalu backup dataset sebelum update:

```
dataset_transaksi_bengkel_las_130.xlsx
dataset_transaksi_bengkel_las_130_backup_YYYYMMDD.xlsx
```

---

**Note:** Format dataset ini adalah contoh. Sesuaikan dengan kebutuhan bisnis Anda.
