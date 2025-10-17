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
        Schema::create('training_data', function (Blueprint $table) {
            $table->id();
            $table->string('produk'); // Pagar, Kanopi, Railing, Teralis, Pintu
            $table->integer('jumlah_unit');
            $table->float('jumlah_lubang')->nullable();
            $table->float('ukuran_m2')->nullable();
            $table->string('jenis_material'); // Hollow, Besi, Stainless, etc
            $table->float('ketebalan_mm');
            $table->string('finishing'); // Cat, Powder Coating, Tanpa Finishing
            $table->string('kerumitan_desain'); // Sederhana, Menengah, Kompleks
            $table->string('metode_hitung'); // Per m², Per Lubang
            $table->decimal('harga_akhir', 15, 2); // Target variable
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_data');
    }
};
