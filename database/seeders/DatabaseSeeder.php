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
            ServicePermissionSeeder::class,
            CustomerPermissionSeeder::class,
            OrderPermissionSeeder::class,
            SettingsSeeder::class,
            ContentSeeder::class,
            ServiceCategorySeeder::class,
            HospitalSeeder::class,
            ServiceSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
