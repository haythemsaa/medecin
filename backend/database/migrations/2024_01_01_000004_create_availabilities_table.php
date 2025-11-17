<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->integer('day_of_week'); // 1=Monday, 7=Sunday
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration')->default(30); // Duration in minutes
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['medecin_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
