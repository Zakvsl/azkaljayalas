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
            // Rename and add columns to match ML model
            $table->string('produk')->nullable()->after('user_id'); // Produk (Pagar, Kanopi, etc)
            $table->integer('jumlah_unit')->default(1)->after('produk');
            $table->float('jumlah_lubang')->nullable()->after('jumlah_unit');
            $table->float('ukuran_m2')->nullable()->after('jumlah_lubang');
            $table->string('jenis_material')->nullable()->after('ukuran_m2');
            $table->float('ketebalan_mm')->nullable()->after('jenis_material');
            $table->string('finishing')->nullable()->after('ketebalan_mm');
            $table->string('kerumitan_desain')->nullable()->after('finishing'); // Sederhana, Menengah, Kompleks
            $table->string('metode_hitung')->nullable()->after('kerumitan_desain'); // Per m² or Per Lubang
            
            // Keep original columns for backward compatibility
            // estimated_price will be the ML prediction
            // actual_price set by admin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_estimates', function (Blueprint $table) {
            $table->dropColumn([
                'produk', 'jumlah_unit', 'jumlah_lubang', 'ukuran_m2',
                'jenis_material', 'ketebalan_mm', 'finishing', 
                'kerumitan_desain', 'metode_hitung'
            ]);
        });
    }
};
