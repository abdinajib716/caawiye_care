<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OrderPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear permission cache first
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create order permissions
        $permissions = [
            'order.view',
            'order.create',
            'order.edit',
            'order.delete',
            'order.restore',
            'order.force_delete',
        ];

        // Create permissions first
        $createdPermissions = [];
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ], [
                'group_name' => 'order',
            ]);
            $createdPermissions[] = $permission;
        }

        // Clear permission cache again after creating
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Assign permissions to Superadmin role
        $superAdminRole = Role::where('name', 'Superadmin')->first();
        if ($superAdminRole) {
            foreach ($createdPermissions as $permission) {
                $superAdminRole->givePermissionTo($permission);
            }
        }

        // Assign basic permissions to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminPermissions = [
                'order.view',
                'order.create',
                'order.edit',
                'order.delete',
            ];
            foreach ($adminPermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission) {
                    $adminRole->givePermissionTo($permission);
                }
            }
        }

        // Clear cache one final time
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Note: Only assign to existing roles (Superadmin and Admin)
        // Other roles like Manager, Editor, User may not exist in this system
    }
}

