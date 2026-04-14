<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ScanImagingPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define scan imaging permissions
        $permissions = [
            'scan_imaging_service.view',
            'scan_imaging_service.create',
            'scan_imaging_service.edit',
            'scan_imaging_service.delete',
            'scan_imaging_booking.view',
            'scan_imaging_booking.create',
            'scan_imaging_booking.edit',
            'scan_imaging_booking.delete',
        ];

        // Clear cache BEFORE creating (important!)
        $this->command->info('Clearing permission cache...');
        \Artisan::call('permission:clear-cache', ['--force' => true]);

        // Create permissions with group_name
        foreach ($permissions as $permission) {
            $groupName = str_starts_with($permission, 'scan_imaging_booking.') ? 'scan_imaging_booking' : 'scan_imaging_service';
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

        $this->command->info('Scan imaging permissions created successfully.');
    }
}
