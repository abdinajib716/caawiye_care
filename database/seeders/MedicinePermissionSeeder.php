<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MedicinePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define all medicine-related permissions
        $permissions = [
            // Medicine Orders
            'medicine_order.view',
            'medicine_order.create',
            'medicine_order.edit',
            'medicine_order.delete',
            
            // Medicine Items (catalog)
            'medicine.view',
            'medicine.create',
            'medicine.edit',
            'medicine.delete',
            
            // Suppliers
            'supplier.view',
            'supplier.create',
            'supplier.edit',
            'supplier.delete',
            
            // Delivery Locations
            'delivery_location.view',
            'delivery_location.create',
            'delivery_location.edit',
            'delivery_location.delete',
            
            // Delivery Prices
            'delivery_price.view',
            'delivery_price.create',
            'delivery_price.edit',
            'delivery_price.delete',
        ];

        // Clear cache BEFORE creating
        $this->command->info('Clearing permission cache...');
        \Artisan::call('permission:clear-cache', ['--force' => true]);
        
        // Create permissions with group_name
        foreach ($permissions as $permission) {
            $groupName = explode('.', $permission)[0]; // Extract group name from permission
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['group_name' => $groupName]
            );
        }

        // Clear cache after creating
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Refresh permissions to ensure they're loaded
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Assign to Superadmin role (ADD permissions, don't replace)
        $superAdminRole = Role::where('name', 'Superadmin')->first();
        if ($superAdminRole) {
            $permissionModels = Permission::whereIn('name', $permissions)->get();
            // Use givePermissionTo to ADD, not syncPermissions which REPLACES
            foreach ($permissionModels as $permission) {
                if (!$superAdminRole->hasPermissionTo($permission)) {
                    $superAdminRole->givePermissionTo($permission);
                }
            }
        }

        // Assign to Admin role (view and create only)
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminPermissions = [
                'medicine_order.view',
                'medicine_order.create',
                'medicine.view',
                'supplier.view',
                'delivery_location.view',
                'delivery_price.view',
            ];
            $permissionModels = Permission::whereIn('name', $adminPermissions)->get();
            $adminRole->givePermissionTo($permissionModels);
        }

        // Final cache clear
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Medicine permissions created successfully.');
    }
}
