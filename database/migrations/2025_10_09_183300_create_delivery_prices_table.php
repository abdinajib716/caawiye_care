<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pickup_location_id')->constrained('delivery_locations')->cascadeOnDelete();
            $table->foreignId('dropoff_location_id')->constrained('delivery_locations')->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->timestamps();
            
            // Ensure unique route combinations
            $table->unique(['pickup_location_id', 'dropoff_location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_prices');
    }
};
