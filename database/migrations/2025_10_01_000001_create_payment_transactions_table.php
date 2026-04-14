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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique()->nullable(); // WaafiPay transaction ID
            $table->string('reference_id')->unique(); // Our internal reference
            $table->string('invoice_id')->nullable(); // Invoice/Order ID
            
            // Payment details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('payment_method')->default('MWALLET_ACCOUNT'); // WaafiPay payment method
            $table->string('provider')->nullable(); // EVC PLUS, JEEB, ZAAD, SAHAL
            
            // Customer information
            $table->string('customer_name');
            $table->string('customer_phone'); // Full phone with country code
            $table->unsignedBigInteger('customer_id')->nullable(); // Link to customers table if exists
            
            // Status tracking
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'expired', 'cancelled'])->default('pending');
            $table->text('description')->nullable();
            $table->text('error_message')->nullable();
            
            // WaafiPay response data
            $table->json('request_payload')->nullable(); // Store the request sent to WaafiPay
            $table->json('response_data')->nullable(); // Store the response from WaafiPay
            $table->string('response_code')->nullable();
            $table->text('response_message')->nullable();
            
            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            
            // Indexes for performance
            $table->index('transaction_id');
            $table->index('reference_id');
            $table->index('invoice_id');
            $table->index('status');
            $table->index('customer_phone');
            $table->index('customer_id');
            $table->index('provider');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};

