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
        // Products table
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('base_price', 15, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Materials table
        if (!Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price_per_kg', 10, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Finishings table
        if (!Schema::hasTable('finishings')) {
            Schema::create('finishings', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price_multiplier', 5, 2)->default(1.0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Kerumitans table
        if (!Schema::hasTable('kerumitans')) {
            Schema::create('kerumitans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price_multiplier', 5, 2)->default(1.0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Ketebalans table
        if (!Schema::hasTable('ketebalans')) {
            Schema::create('ketebalans', function (Blueprint $table) {
                $table->id();
                $table->decimal('thickness_mm', 5, 2);
                $table->text('description')->nullable();
                $table->decimal('price_multiplier', 5, 2)->default(1.0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ketebalans');
        Schema::dropIfExists('kerumitans');
        Schema::dropIfExists('finishings');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('products');
    }
};
