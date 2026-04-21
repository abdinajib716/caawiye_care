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
        if (Schema::hasTable('lab_test_bookings') && ! Schema::hasColumn('lab_test_bookings', 'provider_payment_status')) {
            Schema::table('lab_test_bookings', function (Blueprint $table) {
                $table->string('provider_payment_status')->default('unpaid')->after('status');
            });
        }

        if (Schema::hasTable('scan_imaging_bookings') && ! Schema::hasColumn('scan_imaging_bookings', 'provider_payment_status')) {
            Schema::table('scan_imaging_bookings', function (Blueprint $table) {
                $table->string('provider_payment_status')->default('unpaid')->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lab_test_bookings') && Schema::hasColumn('lab_test_bookings', 'provider_payment_status')) {
            Schema::table('lab_test_bookings', function (Blueprint $table) {
                $table->dropColumn('provider_payment_status');
            });
        }

        if (Schema::hasTable('scan_imaging_bookings') && Schema::hasColumn('scan_imaging_bookings', 'provider_payment_status')) {
            Schema::table('scan_imaging_bookings', function (Blueprint $table) {
                $table->dropColumn('provider_payment_status');
            });
        }
    }
};
