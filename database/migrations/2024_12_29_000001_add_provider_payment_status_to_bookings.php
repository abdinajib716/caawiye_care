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
        // Add provider_payment_status to lab_test_bookings
        Schema::table('lab_test_bookings', function (Blueprint $table) {
            $table->string('provider_payment_status')->default('unpaid')->after('status');
        });

        // Add provider_payment_status to scan_imaging_bookings
        Schema::table('scan_imaging_bookings', function (Blueprint $table) {
            $table->string('provider_payment_status')->default('unpaid')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_test_bookings', function (Blueprint $table) {
            $table->dropColumn('provider_payment_status');
        });

        Schema::table('scan_imaging_bookings', function (Blueprint $table) {
            $table->dropColumn('provider_payment_status');
        });
    }
};
