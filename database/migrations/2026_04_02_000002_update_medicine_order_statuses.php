<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE medicine_orders SET status = 'in_office' WHERE status = 'processing'");
        DB::statement("UPDATE medicine_orders SET status = 'delivered' WHERE status = 'completed'");

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE medicine_orders MODIFY status ENUM('pending', 'in_office', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        DB::statement("UPDATE medicine_orders SET status = 'processing' WHERE status = 'in_office'");
        DB::statement("UPDATE medicine_orders SET status = 'completed' WHERE status = 'delivered'");

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE medicine_orders MODIFY status ENUM('pending', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};
