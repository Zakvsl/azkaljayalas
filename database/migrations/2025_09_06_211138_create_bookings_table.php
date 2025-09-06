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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('prediction_id')->nullable()->constrained();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->string('location_lat')->nullable(); // Google Maps latitude
            $table->string('location_lng')->nullable(); // Google Maps longitude
            $table->date('survey_date');
            $table->time('survey_time');
            $table->enum('status', ['baru', 'diproses', 'selesai'])->default('baru');
            $table->text('notes')->nullable();
            $table->string('booking_id')->unique(); // ID unik untuk booking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
