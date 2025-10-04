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
        Schema::table('services', function (Blueprint $table) {
            // Add service type field
            $table->string('service_type', 50)->default('standard')->after('status');
            
            // Add flag to indicate if service has custom fields
            $table->boolean('has_custom_fields')->default(false)->after('service_type');
            
            // Add JSON column to store custom field configuration
            $table->json('custom_fields_config')->nullable()->after('has_custom_fields');
            
            // Add indexes for better performance
            $table->index('service_type');
            $table->index('has_custom_fields');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['service_type']);
            $table->dropIndex(['has_custom_fields']);
            
            // Drop columns
            $table->dropColumn([
                'service_type',
                'has_custom_fields',
                'custom_fields_config',
            ]);
        });
    }
};

