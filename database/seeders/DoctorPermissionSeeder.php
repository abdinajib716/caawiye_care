<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DoctorPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define doctor permissions
        $permissions = [
            'doctor.view',
            'doctor.create',
            'doctor.edit',
            'doctor.delete',
        ];

        // Clear permission cache BEFORE creating new permissions
        $this->command->info('Clearing permission cache...');
        \Artisan::call('permission:clear-cache', ['--force' => true]);

        // Create permissions with group_name
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['group_name' => 'doctor']
            );
        }

        // Clear permission cache again to ensure new permissions are recognized
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Assign all doctor permissions to Superadmin role
        $superAdminRole = Role::where('name', 'Superadmin')->first();
        if ($superAdminRole) {
            foreach ($permissions as $permission) {
                $superAdminRole->givePermissionTo($permission);
            }
        }

        // Final cache clear
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Doctor permissions created and assigned to Superadmin role.');
    }
}

