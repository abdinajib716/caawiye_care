<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ServicePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create service permissions
        $permissions = [
            'service.view',
            'service.create',
            'service.edit',
            'service.delete',
            'service.restore',
            'service.force_delete',
        ];

        // Create permissions first
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            foreach ($permissions as $permission) {
                $superAdminRole->givePermissionTo($permission);
            }
        }

        // Assign basic permissions to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminPermissions = [
                'service.view',
                'service.create',
                'service.edit',
                'service.delete',
            ];
            foreach ($adminPermissions as $permission) {
                $adminRole->givePermissionTo($permission);
            }
        }

        // Assign permissions to Manager role
        $managerRole = Role::where('name', 'Manager')->first();
        if ($managerRole) {
            $managerPermissions = [
                'service.view',
                'service.create',
                'service.edit',
            ];
            foreach ($managerPermissions as $permission) {
                $managerRole->givePermissionTo($permission);
            }
        }

        // Assign view permission to User role
        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $userRole->givePermissionTo('service.view');
        }
    }
}
