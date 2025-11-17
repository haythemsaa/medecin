<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medecins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('cin', 8)->unique();
            $table->string('cin_file_recto')->nullable();
            $table->string('cin_file_verso')->nullable();
            $table->string('ordre_number')->unique();
            $table->string('ordre_certificate')->nullable();
            $table->string('diploma_file')->nullable();
            $table->string('rcp_attestation')->nullable();
            $table->date('rcp_expiry_date')->nullable();
            $table->string('rib')->nullable();
            $table->string('speciality');
            $table->json('sub_specialities')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->json('consultation_languages')->nullable(); // ['fr', 'ar', 'en']
            $table->decimal('consultation_price', 8, 2)->default(60.00);
            $table->decimal('urgent_consultation_price', 8, 2)->default(80.00);
            $table->enum('validation_status', ['pending', 'validated', 'rejected', 'incomplete'])->default('pending');
            $table->text('validation_notes')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('consultation_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('ordre_number');
            $table->index('speciality');
            $table->index('validation_status');
            $table->index('rating_average');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medecins');
    }
};
