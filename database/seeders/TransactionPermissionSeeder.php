<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TransactionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear permission cache first
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create transaction permissions
        $permissions = [
            'transaction.view',
            'transaction.create',
            'transaction.edit',
            'transaction.delete',
        ];

        // Create permissions first
        $createdPermissions = [];
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ], [
                'group_name' => 'transaction',
            ]);
            $createdPermissions[] = $permission;
            $this->command->info("Created permission: {$permissionName}");
        }

        // Clear permission cache again after creating
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Assign permissions to Superadmin role
        $superAdminRole = Role::where('name', 'Superadmin')->first();
        if ($superAdminRole) {
            foreach ($createdPermissions as $permission) {
                $superAdminRole->givePermissionTo($permission);
            }
            $this->command->info('Assigned transaction permissions to Superadmin role');
        }

        // Assign basic permissions to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminPermissions = [
                'transaction.view',
                'transaction.create',
                'transaction.edit',
            ];
            foreach ($adminPermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission) {
                    $adminRole->givePermissionTo($permission);
                }
            }
            $this->command->info('Assigned transaction permissions to Admin role');
        }

        // Clear cache one final time
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Transaction permissions created successfully!');
    }
}

