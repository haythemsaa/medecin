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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['card', 'cash', 'e-dinar', 'mobile_money']);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('transaction_id')->unique();
            $table->string('gateway_reference')->nullable(); // Reference from payment gateway
            $table->timestamp('paid_at')->nullable();
            $table->json('payment_metadata')->nullable(); // Additional payment data

            // Refund fields
            $table->enum('refund_status', ['none', 'requested', 'completed', 'rejected'])->default('none');
            $table->text('refund_reason')->nullable();
            $table->timestamp('refund_requested_at')->nullable();
            $table->timestamp('refund_processed_at')->nullable();
            $table->text('refund_admin_notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['appointment_id']);
            $table->index('transaction_id');
        });

        // Add payment_status to appointments table if it doesn't exist
        if (!Schema::hasColumn('appointments', 'payment_status')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'refunded'])
                    ->default('unpaid')
                    ->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('appointments', 'payment_status')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('payment_status');
            });
        }

        Schema::dropIfExists('payments');
    }
};
