<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('survey_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('surveyed_by')->constrained('users')->onDelete('cascade'); // Admin yang survei
            
            // Data hasil survei untuk ML (15 features)
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('material_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('finishing_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('kerumitan_id')->nullable()->constrained('kerumitans')->onDelete('set null');
            $table->foreignId('ketebalan_id')->nullable()->constrained('ketebalans')->onDelete('set null');
            
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->integer('quantity')->default(1);
            
            // ML Prediction Result
            $table->decimal('ai_estimated_price', 15, 2)->nullable();
            $table->decimal('admin_adjusted_price', 15, 2)->nullable(); // Admin bisa koreksi
            $table->decimal('final_price', 15, 2)->nullable(); // Harga final yang dikirim ke user
            
            // Additional survey notes
            $table->text('survey_notes')->nullable();
            $table->json('survey_photos')->nullable(); // JSON array of photo paths
            $table->text('special_requirements')->nullable();
            
            $table->dateTime('surveyed_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_results');
    }
};
