<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->decimal('quality_rating', 2, 1); // 1-5
            $table->decimal('listening_rating', 2, 1); // 1-5
            $table->decimal('clarity_rating', 2, 1); // 1-5
            $table->decimal('punctuality_rating', 2, 1); // 1-5
            $table->decimal('value_rating', 2, 1); // 1-5
            $table->decimal('overall_rating', 2, 1); // Average of above
            $table->text('comment')->nullable();
            $table->boolean('would_recommend')->default(true);
            $table->enum('status', ['pending', 'approved', 'rejected', 'flagged'])->default('approved');
            $table->text('medecin_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['medecin_id', 'status']);
            $table->index('overall_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
