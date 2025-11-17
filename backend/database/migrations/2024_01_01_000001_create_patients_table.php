<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('first_name_ar')->nullable();
            $table->string('last_name_ar')->nullable();
            $table->date('birth_date');
            $table->string('cin', 8)->unique();
            $table->string('cin_file_recto')->nullable();
            $table->string('cin_file_verso')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('governorate')->nullable();
            $table->string('delegation')->nullable();
            $table->text('address')->nullable();
            $table->enum('health_coverage', ['cnam', 'mutuelle', 'none'])->default('none');
            $table->string('cnam_number')->nullable();
            $table->string('mutuelle_name')->nullable();
            $table->string('blood_type', 5)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('cin');
            $table->index('governorate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
