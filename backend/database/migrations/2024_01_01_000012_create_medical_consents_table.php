<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['single', 'extended', 'emergency'])->default('single');
            $table->integer('duration_months')->nullable(); // For extended type
            $table->timestamp('valid_from');
            $table->timestamp('valid_until');
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_renew')->default(false);
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'medecin_id', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
        });

        Schema::create('medical_record_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->foreignId('consent_id')->nullable()->constrained('medical_consents')->onDelete('set null');
            $table->string('accessed_section');
            $table->string('ip_address', 45);
            $table->timestamp('accessed_at');
            $table->timestamps();

            $table->index(['patient_id', 'accessed_at']);
            $table->index(['medecin_id', 'accessed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_record_accesses');
        Schema::dropIfExists('medical_consents');
    }
};
