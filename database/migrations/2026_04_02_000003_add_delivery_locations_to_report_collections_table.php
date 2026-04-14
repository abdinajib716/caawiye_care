<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_collections', function (Blueprint $table) {
            $table->foreignId('pickup_location_id')
                ->nullable()
                ->after('delivery_required')
                ->constrained('delivery_locations')
                ->nullOnDelete();

            $table->foreignId('dropoff_location_id')
                ->nullable()
                ->after('pickup_location_id')
                ->constrained('delivery_locations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('report_collections', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pickup_location_id');
            $table->dropConstrainedForeignId('dropoff_location_id');
        });
    }
};
