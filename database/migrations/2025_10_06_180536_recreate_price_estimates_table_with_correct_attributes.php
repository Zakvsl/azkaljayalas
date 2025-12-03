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
        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();
        
        // Drop old table and recreate with correct attributes
        Schema::dropIfExists('price_estimates');
        
        Schema::create('price_estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Atribut sesuai summary_app.md
            $table->enum('jenis_produk', [
                'Pagar',
                'Kanopi',
                'Railing',
                'Teralis',
                'Pintu',
                'Tangga'
            ]);
            $table->integer('jumlah_unit')->default(1);
            
            // Conditional: Hanya untuk Teralis (per lubang)
            $table->integer('jumlah_lubang')->nullable();
            
            // Conditional: Untuk non-Teralis (per m²)
            $table->decimal('ukuran_m2', 8, 2)->nullable();
            
            // Material
            $table->enum('jenis_material', [
                'hollow',
                'besi_siku',
                'aluminium',
                'stainless',
                'plat'
            ]);
            
            // Profile size - Conditional: Tidak untuk plat
            $table->string('profile_size')->nullable(); // e.g., "2x4", "3x3", "1.5inch"
            
            // Ketebalan dalam mm
            $table->decimal('ketebalan_mm', 5, 2);
            
            // Finishing
            $table->enum('finishing', [
                'cat_biasa',
                'cat_epoxy',
                'powder_coating',
                'galvanis'
            ])->default('cat_biasa');
            
            // Kerumitan desain (1=sederhana, 2=menengah, 3=kompleks)
            $table->tinyInteger('kerumitan_desain')->default(1);
            
            // Harga hasil estimasi ML
            $table->decimal('harga_akhir', 12, 2);
            
            // Status dan notes
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['jenis_produk', 'jenis_material']);
            $table->index('status');
        });
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('price_estimates');
        Schema::enableForeignKeyConstraints();
    }
};
