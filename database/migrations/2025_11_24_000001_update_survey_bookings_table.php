<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('survey_bookings', function (Blueprint $table) {
            // Add map coordinates
            $table->decimal('latitude', 10, 7)->nullable()->after('location');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            
            // Add time slot info
            $table->time('preferred_time')->nullable()->after('preferred_date');
            
            // Add admin confirmation
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            $table->dateTime('confirmed_at')->nullable()->after('confirmed_by');
            $table->text('cancel_reason')->nullable()->after('confirmed_at');
            
            // Add WhatsApp notification tracking
            $table->boolean('whatsapp_sent')->default(false)->after('cancel_reason');
            $table->dateTime('whatsapp_sent_at')->nullable()->after('whatsapp_sent');
        });
    }

    public function down()
    {
        Schema::table('survey_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'preferred_time',
                'confirmed_by',
                'confirmed_at',
                'cancel_reason',
                'whatsapp_sent',
                'whatsapp_sent_at'
            ]);
        });
    }
};
