<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->json('allergies')->nullable(); // List of allergies
            $table->json('chronic_diseases')->nullable(); // List of chronic diseases
            $table->json('current_treatments')->nullable(); // Current medications
            $table->json('vaccinations')->nullable(); // Vaccination history
            $table->text('medical_history')->nullable();
            $table->text('family_medical_history')->nullable();
            $table->timestamps();

            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
