<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Notification channels
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->boolean('push_enabled')->default(true);

            // Notification types
            $table->boolean('appointment_reminders')->default(true);
            $table->boolean('appointment_confirmations')->default(true);
            $table->boolean('appointment_cancellations')->default(true);
            $table->boolean('new_messages')->default(true);
            $table->boolean('prescription_ready')->default(true);
            $table->boolean('review_reminders')->default(true);
            $table->boolean('marketing_emails')->default(false);

            // Reminder timing (hours before appointment)
            $table->json('reminder_hours_before')->default(json_encode([24, 1])); // 24h and 1h before

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
