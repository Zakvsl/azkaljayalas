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
        Schema::create('ketebalan', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('value', 8, 2); // nilai ketebalan dalam mm
            $table->text('description')->nullable();
            $table->decimal('price_factor', 8, 2)->default(1.0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ketebalan');
    }
};
