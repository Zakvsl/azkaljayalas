<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop old orders table if exists and recreate with new structure
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('orders');
        
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Format: ORD-YYYYMMDD-XXXX
            $table->foreignId('survey_booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('survey_result_id')->nullable()->constrained()->onDelete('set null');
            
            // Order details
            $table->decimal('total_price', 15, 2);
            $table->decimal('dp_paid', 15, 2)->default(0);
            $table->decimal('remaining_paid', 15, 2)->default(0);
            
            // Order status: pending_dp, in_progress, completed, cancelled
            $table->enum('status', [
                'pending_dp',           // Menunggu pembayaran DP
                'dp_pending_confirm',   // DP sudah diupload, menunggu konfirmasi admin
                'in_progress',          // Sedang dikerjakan
                'ready_for_pickup',     // Siap diambil
                'completed',            // Selesai
                'cancelled'             // Dibatalkan
            ])->default('pending_dp');
            
            // Progress tracking
            $table->integer('progress_percentage')->default(0); // 0-100%
            $table->text('current_stage')->nullable(); // Tahap pengerjaan saat ini
            $table->json('progress_updates')->nullable(); // Array of progress updates
            
            // Completion
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('order_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
