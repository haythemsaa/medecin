<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_consultation_questionnaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->string('chief_complaint');
            $table->string('symptom_duration')->nullable();
            $table->integer('symptom_intensity')->nullable(); // 1-10 scale
            $table->json('current_medications')->nullable();
            $table->json('allergies')->nullable();
            $table->json('previous_conditions')->nullable();
            $table->text('family_history')->nullable();
            $table->json('lifestyle')->nullable(); // smoking, alcohol, exercise, etc.
            $table->text('additional_notes')->nullable();
            $table->json('answers')->nullable(); // Custom template answers
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('appointment_id');
            $table->index('patient_id');
            $table->index('medecin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_consultation_questionnaires');
    }
};
