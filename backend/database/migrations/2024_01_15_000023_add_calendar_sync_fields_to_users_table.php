<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('google_calendar_token')->nullable();
            $table->text('google_calendar_refresh_token')->nullable();
            $table->text('outlook_calendar_token')->nullable();
            $table->text('outlook_calendar_refresh_token')->nullable();
            $table->boolean('calendar_auto_sync')->default(false);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->string('google_event_id')->nullable();
            $table->string('outlook_event_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_calendar_token',
                'google_calendar_refresh_token',
                'outlook_calendar_token',
                'outlook_calendar_refresh_token',
                'calendar_auto_sync',
            ]);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['google_event_id', 'outlook_event_id']);
        });
    }
};
