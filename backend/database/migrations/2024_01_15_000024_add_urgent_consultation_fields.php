<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->boolean('is_urgent')->default(false)->after('status');
            $table->decimal('urgent_fee', 10, 2)->nullable()->after('is_urgent');
            $table->timestamp('urgent_requested_at')->nullable()->after('urgent_fee');
        });

        Schema::table('medecins', function (Blueprint $table) {
            $table->boolean('accepts_urgent')->default(false)->after('tarif');
            $table->decimal('urgent_surcharge_percentage', 5, 2)->default(50.00)->after('accepts_urgent');
            $table->integer('urgent_response_time')->default(60)->after('urgent_surcharge_percentage')->comment('Response time in minutes');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['is_urgent', 'urgent_fee', 'urgent_requested_at']);
        });

        Schema::table('medecins', function (Blueprint $table) {
            $table->dropColumn(['accepts_urgent', 'urgent_surcharge_percentage', 'urgent_response_time']);
        });
    }
};
