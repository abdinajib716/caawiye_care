<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create customer permissions
        $permissions = [
            'customer.view',
            'customer.create',
            'customer.edit',
            'customer.delete',
            'customer.restore',
            'customer.force_delete',
        ];

        // Create permissions first
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to Superadmin role
        $superAdminRole = Role::where('name', 'Superadmin')->first();
        if ($superAdminRole) {
            foreach ($permissions as $permission) {
                $superAdminRole->givePermissionTo($permission);
            }
        }

        // Assign basic permissions to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminPermissions = [
                'customer.view',
                'customer.create',
                'customer.edit',
                'customer.delete',
            ];
            foreach ($adminPermissions as $permission) {
                $adminRole->givePermissionTo($permission);
            }
        }

        // Note: Only assign to existing roles (Superadmin and Admin)
        // Other roles like Manager, Editor, User may not exist in this system
    }
}
