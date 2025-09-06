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
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->foreignId('finishing_id')->constrained()->onDelete('cascade');
            $table->foreignId('kerumitan_id')->constrained()->onDelete('cascade');
            $table->foreignId('ketebalan_id')->constrained()->onDelete('cascade');
            $table->decimal('width', 8, 2);
            $table->decimal('height', 8, 2);
            $table->decimal('length', 8, 2)->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 12, 2);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'processed', 'completed', 'cancelled'])->default('pending');
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
