<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('price_estimates', function (Blueprint $table) {
            // Atribut Material - Profile Size (jika belum ada)
            if (!Schema::hasColumn('price_estimates', 'profile_size')) {
                $table->string('profile_size', 50)->nullable()->after('jenis_material')
                    ->comment('Ukuran profile material (mis: 40x40, 2x4, 3x3)');
            }
            
            // Atribut Lokasi & Akses
            $table->integer('jarak_lokasi')->default(5)->after('profile_size')
                ->comment('Jarak ke lokasi proyek dalam KM (0-100)');
            
            $table->enum('tingkat_kesulitan_akses', ['Mudah', 'Sedang', 'Sulit'])
                ->default('Mudah')->after('jarak_lokasi')
                ->comment('Tingkat kesulitan akses lokasi instalasi');
            
            $table->enum('tinggi_instalasi', ['Di Tanah', 'Lantai 2', 'Lantai 3+'])
                ->default('Di Tanah')->after('tingkat_kesulitan_akses')
                ->comment('Ketinggian instalasi');
            
            // Atribut Biaya Karyawan - INI YANG DIMINTA DOSEN!
            $table->integer('jumlah_tukang')->default(2)->after('tinggi_instalasi')
                ->comment('Jumlah tukang las yang dibutuhkan (1-5 orang)');
            
            $table->integer('estimasi_hari_kerja')->default(3)->after('jumlah_tukang')
                ->comment('Estimasi durasi pengerjaan dalam hari (1-30)');
            
            $table->integer('biaya_tukang_per_hari')->default(200000)->after('estimasi_hari_kerja')
                ->comment('Biaya upah tukang per hari (Rp 150k-250k)');
            
            $table->bigInteger('total_biaya_karyawan')->default(0)->after('biaya_tukang_per_hari')
                ->comment('Total biaya karyawan = jumlah_tukang × hari_kerja × biaya_per_hari');
            
            // Atribut Teknis (Auto-calculated)
            $table->integer('jumlah_sambungan')->nullable()->after('total_biaya_karyawan')
                ->comment('Estimasi jumlah titik sambungan las (10-500)');
            
            $table->integer('jumlah_potongan')->nullable()->after('jumlah_sambungan')
                ->comment('Estimasi jumlah potongan material (5-200)');
            
            $table->decimal('volume_material', 10, 4)->nullable()->after('jumlah_potongan')
                ->comment('Volume material estimasi dalam m³');
            
            $table->integer('complexity_score')->nullable()->after('volume_material')
                ->comment('Skor kompleksitas total (1-10)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_estimates', function (Blueprint $table) {
            $table->dropColumn([
                'jarak_lokasi',
                'tingkat_kesulitan_akses',
                'tinggi_instalasi',
                'jumlah_tukang',
                'estimasi_hari_kerja',
                'biaya_tukang_per_hari',
                'total_biaya_karyawan',
                'jumlah_sambungan',
                'jumlah_potongan',
                'volume_material',
                'complexity_score'
            ]);
            
            // Profile size tidak dihapus karena mungkin sudah ada dari migration sebelumnya
        });
    }
};
