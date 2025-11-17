<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->dateTime('appointment_date');
            $table->integer('duration')->default(30); // in minutes
            $table->enum('type', ['video', 'phone'])->default('video');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('confirmed');
            $table->text('reason')->nullable();
            $table->json('symptoms')->nullable();
            $table->json('attached_documents')->nullable();
            $table->decimal('price', 8, 2);
            $table->boolean('is_urgent')->default(false);
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->enum('cancelled_by', ['patient', 'medecin', 'admin'])->nullable();
            $table->decimal('refund_amount', 8, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'appointment_date']);
            $table->index(['medecin_id', 'appointment_date']);
            $table->index(['status', 'appointment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
