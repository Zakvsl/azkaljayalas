<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Payment type: dp (down payment) or full
            $table->enum('payment_type', ['dp', 'full', 'remaining'])->default('dp');
            
            // Amounts
            $table->decimal('total_price', 15, 2); // Total harga final
            $table->decimal('dp_amount', 15, 2)->nullable(); // Nominal DP
            $table->decimal('dp_percentage', 5, 2)->nullable(); // Persentase DP (misal 30%)
            $table->decimal('remaining_amount', 15, 2)->nullable(); // Sisa pembayaran
            $table->decimal('paid_amount', 15, 2)->default(0); // Yang sudah dibayar
            
            // Payment status: pending, waiting_confirmation, confirmed, rejected
            $table->enum('status', ['pending', 'waiting_confirmation', 'confirmed', 'rejected'])->default('pending');
            
            // Payment proof upload
            $table->string('payment_proof')->nullable(); // Path to uploaded image
            $table->dateTime('paid_at')->nullable(); // Kapan user upload bukti
            
            // Admin confirmation
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('confirmed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Payment details
            $table->string('payment_method')->nullable(); // Transfer Bank, Cash, etc
            $table->text('payment_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['survey_booking_id', 'payment_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
