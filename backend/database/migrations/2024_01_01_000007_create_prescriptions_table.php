<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->string('prescription_number')->unique();
            $table->json('medications'); // Array of medications with details
            $table->text('recommendations')->nullable();
            $table->boolean('renewable')->default(false);
            $table->integer('renewable_times')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('digital_signature')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->boolean('is_valid')->default(true);
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('prescription_number');
            $table->index(['patient_id', 'created_at']);
            $table->index(['medecin_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
