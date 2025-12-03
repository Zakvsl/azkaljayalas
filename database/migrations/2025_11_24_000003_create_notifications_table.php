<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Notification type: booking, survey, price_offer, order_progress
            $table->enum('type', ['booking', 'survey', 'price_offer', 'order_progress', 'payment'])->default('booking');
            
            // Reference to related entities
            $table->foreignId('survey_booking_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional metadata
            
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
