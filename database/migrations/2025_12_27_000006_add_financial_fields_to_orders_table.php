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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('provider_cost', 12, 2)->default(0.00)->after('total');
            $table->unsignedBigInteger('provider_id')->nullable()->after('provider_cost');
            $table->string('provider_type')->nullable()->after('provider_id');
            $table->timestamp('revenue_recorded_at')->nullable()->after('completed_at');
            $table->timestamp('refunded_at')->nullable()->after('revenue_recorded_at');
            $table->text('refund_reason')->nullable()->after('refunded_at');
        });

        // Add same fields to lab_test_bookings if not exists
        if (Schema::hasTable('lab_test_bookings') && !Schema::hasColumn('lab_test_bookings', 'provider_cost')) {
            Schema::table('lab_test_bookings', function (Blueprint $table) {
                $table->decimal('provider_cost', 12, 2)->default(0.00)->after('total');
                $table->unsignedBigInteger('provider_id')->nullable()->after('provider_cost');
                $table->timestamp('revenue_recorded_at')->nullable()->after('completed_at');
                $table->timestamp('refunded_at')->nullable()->after('revenue_recorded_at');
                $table->text('refund_reason')->nullable()->after('refunded_at');
            });
        }

        // Add same fields to medicine_orders if not exists
        if (Schema::hasTable('medicine_orders') && !Schema::hasColumn('medicine_orders', 'provider_cost')) {
            Schema::table('medicine_orders', function (Blueprint $table) {
                $table->decimal('provider_cost', 12, 2)->default(0.00)->after('total');
                $table->unsignedBigInteger('provider_id')->nullable()->after('provider_cost');
                $table->timestamp('completed_at')->nullable()->after('status');
                $table->timestamp('revenue_recorded_at')->nullable()->after('completed_at');
                $table->timestamp('refunded_at')->nullable()->after('revenue_recorded_at');
                $table->text('refund_reason')->nullable()->after('refunded_at');
            });
        }

        // Add same fields to scan_imaging_bookings if not exists
        if (Schema::hasTable('scan_imaging_bookings') && !Schema::hasColumn('scan_imaging_bookings', 'provider_cost')) {
            Schema::table('scan_imaging_bookings', function (Blueprint $table) {
                $table->decimal('provider_cost', 12, 2)->default(0.00)->after('total');
                $table->timestamp('revenue_recorded_at')->nullable()->after('completed_at');
                $table->timestamp('refunded_at')->nullable()->after('revenue_recorded_at');
                $table->text('refund_reason')->nullable()->after('refunded_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'provider_cost',
                'provider_id',
                'provider_type',
                'revenue_recorded_at',
                'refunded_at',
                'refund_reason',
            ]);
        });

        if (Schema::hasTable('lab_test_bookings') && Schema::hasColumn('lab_test_bookings', 'provider_cost')) {
            Schema::table('lab_test_bookings', function (Blueprint $table) {
                $table->dropColumn([
                    'provider_cost',
                    'provider_id',
                    'revenue_recorded_at',
                    'refunded_at',
                    'refund_reason',
                ]);
            });
        }

        if (Schema::hasTable('medicine_orders') && Schema::hasColumn('medicine_orders', 'provider_cost')) {
            Schema::table('medicine_orders', function (Blueprint $table) {
                $table->dropColumn([
                    'provider_cost',
                    'provider_id',
                    'completed_at',
                    'revenue_recorded_at',
                    'refunded_at',
                    'refund_reason',
                ]);
            });
        }

        if (Schema::hasTable('scan_imaging_bookings') && Schema::hasColumn('scan_imaging_bookings', 'provider_cost')) {
            Schema::table('scan_imaging_bookings', function (Blueprint $table) {
                $table->dropColumn([
                    'provider_cost',
                    'revenue_recorded_at',
                    'refunded_at',
                    'refund_reason',
                ]);
            });
        }
    }
};
