<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LabTestPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define lab test permissions
        $permissions = [
            'lab_test.view',
            'lab_test.create',
            'lab_test.edit',
            'lab_test.delete',
            'lab_test_booking.view',
            'lab_test_booking.create',
            'lab_test_booking.edit',
            'lab_test_booking.delete',
        ];

        // Clear cache BEFORE creating (important!)
        $this->command->info('Clearing permission cache...');
        \Artisan::call('permission:clear-cache', ['--force' => true]);

        // Create permissions with group_name
        foreach ($permissions as $permission) {
            $groupName = str_starts_with($permission, 'lab_test_booking.') ? 'lab_test_booking' : 'lab_test';
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['group_name' => $groupName]
            );
        }

        // Clear cache after creating
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Assign to Superadmin role
        $superAdminRole = Role::where('name', 'Superadmin')->first();
        if ($superAdminRole) {
            $permissionModels = Permission::whereIn('name', $permissions)->get();
            foreach ($permissionModels as $permission) {
                if (!$superAdminRole->hasPermissionTo($permission)) {
                    $superAdminRole->givePermissionTo($permission);
                }
            }
        }

        // Final cache clear
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Lab test permissions created successfully.');
    }
}
