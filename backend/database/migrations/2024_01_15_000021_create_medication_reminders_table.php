<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('prescription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('medication_name');
            $table->string('dosage');
            $table->enum('frequency', [
                'once_daily',
                'twice_daily',
                'three_times_daily',
                'four_times_daily',
                'as_needed',
                'custom'
            ]);
            $table->json('reminder_times'); // Array of times like ["08:00", "14:00", "20:00"]
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('user_id');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_reminders');
    }
};
