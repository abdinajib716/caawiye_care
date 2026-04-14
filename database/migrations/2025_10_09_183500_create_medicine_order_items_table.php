<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_order_id')->constrained('medicine_orders')->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
            $table->string('medicine_name'); // Store name for history
            $table->integer('quantity');
            $table->decimal('cost', 10, 2); // Cost per unit
            $table->decimal('profit', 10, 2)->default(0); // Profit value (amount or percentage)
            $table->enum('profit_type', ['fixed', 'percentage'])->default('fixed'); // Type of profit
            $table->decimal('profit_amount', 10, 2)->default(0); // Calculated profit amount
            $table->decimal('unit_price', 10, 2); // Same as cost (cost per unit)
            $table->decimal('total_price', 10, 2); // (cost × quantity) + profit_amount
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_order_items');
    }
};
