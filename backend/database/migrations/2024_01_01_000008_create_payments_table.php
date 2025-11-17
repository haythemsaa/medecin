<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->string('transaction_id')->unique();
            $table->enum('payment_method', ['card', 'e_dinar', 'mobile_money', 'postal_mandate']);
            $table->decimal('amount', 10, 2);
            $table->decimal('medecin_amount', 10, 2); // 85% of amount
            $table->decimal('platform_commission', 10, 2); // 15% of amount
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->text('payment_details')->nullable(); // JSON with gateway response
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->text('refund_reason')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_pdf_path')->nullable();
            $table->timestamps();

            $table->index('transaction_id');
            $table->index(['patient_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
