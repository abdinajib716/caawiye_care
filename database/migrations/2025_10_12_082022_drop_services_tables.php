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
        // First, drop foreign key constraint from order_items if it exists
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['service_id']);
            });
        }

        // Drop service-related tables
        Schema::dropIfExists('service_field_data');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't recreate these tables in down() as they're being permanently removed
        // If needed, restore from previous migrations
    }
};
