<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('filename'); // UUID filename
            $table->string('original_name'); // Original uploaded filename
            $table->string('path'); // Storage path
            $table->string('mime_type');
            $table->bigInteger('size'); // File size in bytes
            $table->enum('type', [
                'profile_photo',
                'medical_document',
                'prescription',
                'report',
                'imaging',
                'other'
            ]);
            $table->string('related_type')->nullable(); // Polymorphic relation type
            $table->unsignedBigInteger('related_id')->nullable(); // Polymorphic relation ID
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
