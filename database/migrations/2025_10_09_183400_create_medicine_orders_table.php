<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Delivery info (optional)
            $table->boolean('requires_delivery')->default(false);
            $table->foreignId('pickup_location_id')->nullable()->constrained('delivery_locations')->nullOnDelete();
            $table->foreignId('dropoff_location_id')->nullable()->constrained('delivery_locations')->nullOnDelete();
            $table->decimal('delivery_price', 10, 2)->default(0);
            
            // Financial
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            
            // Payment
            $table->string('payment_method')->nullable();
            $table->string('payment_phone')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('payment_reference')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_orders');
    }
};
