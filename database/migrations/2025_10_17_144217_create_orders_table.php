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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone');
            $table->text('address');
            $table->string('project_type'); // pagar, kanopi, railing, etc
            $table->string('material_type'); // hollow, besi, stainless, etc
            $table->json('dimensions')->nullable(); // length, width, height, thickness
            $table->text('description')->nullable();
            $table->decimal('estimated_price', 15, 2)->nullable();
            $table->decimal('actual_price', 15, 2)->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->date('order_date');
            $table->date('completion_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
