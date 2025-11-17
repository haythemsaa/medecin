<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_intakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reminder_id')->constrained('medication_reminders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('taken_at');
            $table->boolean('skipped')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('reminder_id');
            $table->index('user_id');
            $table->index('taken_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_intakes');
    }
};
