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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            
            $table->foreignId('product_id')->constrained();
            $table->foreignId('material_id')->constrained();
            $table->foreignId('finishing_id')->constrained('finishing');
            $table->foreignId('kerumitan_id')->constrained('kerumitan');
            $table->foreignId('ketebalan_id')->constrained('ketebalan');
            $table->decimal('ukuran', 10, 2); // dalam m²
            $table->integer('jumlah_unit')->default(1);
            $table->decimal('predicted_price', 12, 2);
            $table->string('prediction_id')->unique(); // ID unik untuk prediksi
            $table->json('input_data')->nullable(); // menyimpan data input untuk model
            $table->json('model_output')->nullable(); // menyimpan output mentah dari model
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
