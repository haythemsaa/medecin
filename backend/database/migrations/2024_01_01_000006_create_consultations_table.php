<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('chief_complaint')->nullable();
            $table->text('history_of_present_illness')->nullable();
            $table->text('physical_examination')->nullable();
            $table->text('diagnosis')->nullable();
            $table->string('diagnosis_code')->nullable(); // ICD-10 code
            $table->text('treatment_plan')->nullable();
            $table->text('notes')->nullable();
            $table->json('vitals')->nullable(); // BP, temperature, etc.
            $table->json('chat_history')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('follow_up_instructions')->nullable();
            $table->integer('follow_up_days')->nullable();
            $table->enum('connection_quality', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->boolean('technical_issues')->default(false);
            $table->text('technical_issues_description')->nullable();
            $table->timestamps();

            $table->index('appointment_id');
            $table->index(['patient_id', 'created_at']);
            $table->index(['medecin_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
