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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('refund_number')->unique();
            $table->unsignedBigInteger('order_id');
            $table->string('order_type');
            $table->decimal('original_amount', 12, 2);
            $table->decimal('refund_amount', 12, 2);
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'processing', 'completed', 'rejected'])->default('pending');
            $table->boolean('provider_payment_reversed')->default(false);
            $table->timestamp('provider_refund_confirmed_at')->nullable();
            $table->enum('refund_method', ['evc', 'edahab', 'cash', 'bank'])->nullable();
            $table->string('refund_reference')->nullable();
            $table->timestamp('refund_executed_at')->nullable();
            $table->unsignedBigInteger('revenue_reversal_id')->nullable();
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('revenue_reversal_id')->references('id')->on('revenue_ledger')->onDelete('set null');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');

            $table->index('refund_number');
            $table->index('status');
            $table->index(['order_type', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
