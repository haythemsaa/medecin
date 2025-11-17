<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medecins', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('adresse');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('ville')->nullable()->after('longitude');
            $table->string('code_postal', 10)->nullable()->after('ville');
            $table->boolean('show_on_map')->default(true)->after('code_postal');
        });
    }

    public function down(): void
    {
        Schema::table('medecins', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'ville', 'code_postal', 'show_on_map']);
        });
    }
};
