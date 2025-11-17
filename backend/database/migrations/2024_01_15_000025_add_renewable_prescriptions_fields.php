<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->boolean('is_renewable')->default(false)->after('medicaments');
            $table->integer('renewals_allowed')->default(0)->after('is_renewable');
            $table->integer('renewals_used')->default(0)->after('renewals_allowed');
            $table->date('valid_until')->nullable()->after('renewals_used');
            $table->text('renewal_conditions')->nullable()->after('valid_until');
        });

        // Create renewal history table
        Schema::create('prescription_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('patient_notes')->nullable();
            $table->text('medecin_notes')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('medecins')->onDelete('set null');
            $table->timestamps();

            $table->index('prescription_id');
            $table->index('patient_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn([
                'is_renewable',
                'renewals_allowed',
                'renewals_used',
                'valid_until',
                'renewal_conditions',
            ]);
        });

        Schema::dropIfExists('prescription_renewals');
    }
};
