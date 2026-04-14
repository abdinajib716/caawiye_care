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
        Schema::table('doctors', function (Blueprint $table) {
            $table->decimal('appointment_cost', 10, 2)->default(0.00)->after('email');
            $table->decimal('profit', 10, 2)->default(0.00)->after('appointment_cost');
            $table->decimal('total', 10, 2)->default(0.00)->after('profit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['appointment_cost', 'profit', 'total']);
        });
    }
};
