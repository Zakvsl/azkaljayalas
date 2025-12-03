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
            // Rename and add columns to match ML model - check if exists first
            if (!Schema::hasColumn('price_estimates', 'produk')) {
                $table->string('produk')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('price_estimates', 'jumlah_unit')) {
                $table->integer('jumlah_unit')->default(1)->after('produk');
            }
            if (!Schema::hasColumn('price_estimates', 'jumlah_lubang')) {
                $table->float('jumlah_lubang')->nullable()->after('jumlah_unit');
            }
            if (!Schema::hasColumn('price_estimates', 'ukuran_m2')) {
                $table->float('ukuran_m2')->nullable()->after('jumlah_lubang');
            }
            if (!Schema::hasColumn('price_estimates', 'jenis_material')) {
                $table->string('jenis_material')->nullable()->after('ukuran_m2');
            }
            if (!Schema::hasColumn('price_estimates', 'ketebalan_mm')) {
                $table->float('ketebalan_mm')->nullable()->after('jenis_material');
            }
            if (!Schema::hasColumn('price_estimates', 'finishing')) {
                $table->string('finishing')->nullable()->after('ketebalan_mm');
            }
            if (!Schema::hasColumn('price_estimates', 'kerumitan_desain')) {
                $table->string('kerumitan_desain')->nullable()->after('finishing');
            }
            if (!Schema::hasColumn('price_estimates', 'metode_hitung')) {
                $table->string('metode_hitung')->nullable()->after('kerumitan_desain');
            }
            
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
