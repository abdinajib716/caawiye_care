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
            // Drop indexes first if they exist
            try {
                $table->dropIndex(['is_featured']);
            } catch (Exception $e) {
                // Index might not exist
            }

            try {
                $table->dropFullText(['name', 'description']);
            } catch (Exception $e) {
                // Index might not exist
            }

            // Drop columns
            $table->dropColumn([
                'description',
                'sku',
                'is_featured',
                'meta_title',
                'meta_description'
            ]);

            // Recreate fulltext index without description
            $table->fullText(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Add columns back
            $table->text('description')->nullable();
            $table->string('sku', 100)->unique()->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Recreate indexes
            $table->index(['is_featured']);
            $table->dropFullText(['name']);
            $table->fullText(['name', 'description']);
        });
    }
};
