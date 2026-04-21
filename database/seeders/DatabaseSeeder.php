<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RolePermissionSeeder::class,
            SyncPredefinedRolePermissionsSeeder::class,
            CustomerPermissionSeeder::class,
            OrderPermissionSeeder::class,
            AppointmentPermissionSeeder::class,
            SettingsSeeder::class,
            HospitalSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
