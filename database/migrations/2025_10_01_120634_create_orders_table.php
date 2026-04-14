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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            // Customer and Agent
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('agent_id'); // User who created the order

            // Order Totals
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);

            // Payment Details
            $table->string('payment_method'); // 'waafipay' or 'edahab'
            $table->string('payment_provider')->nullable(); // 'EVC PLUS', 'ZAAD', etc.
            $table->string('payment_phone');
            $table->enum('payment_status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            $table->unsignedBigInteger('payment_transaction_id')->nullable(); // Link to payment_transactions

            // Order Status
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('set null');

            // Indexes for Performance
            $table->index('order_number');
            $table->index('customer_id');
            $table->index('agent_id');
            $table->index('payment_status');
            $table->index('status');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
            $table->index(['payment_status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
