<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RestoreMissingPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder ensures ALL existing permissions are assigned to Superadmin role.
     */
    public function run(): void
    {
        $this->command->info('Restoring missing permissions to Superadmin...');
        
        // Clear cache
        \Artisan::call('permission:clear-cache', ['--force' => true]);
        
        // Get Superadmin role
        $superAdminRole = Role::where('name', 'Superadmin')->first();
        
        if (!$superAdminRole) {
            $this->command->error('Superadmin role not found!');
            return;
        }
        
        // Get all permissions
        $allPermissions = Permission::all();
        $currentPermissions = $superAdminRole->permissions->pluck('name')->toArray();
        
        $this->command->info("Total permissions in system: {$allPermissions->count()}");
        $this->command->info("Superadmin currently has: " . count($currentPermissions));
        
        $added = 0;
        
        // Add ALL permissions to Superadmin (without removing existing ones)
        foreach ($allPermissions as $permission) {
            if (!in_array($permission->name, $currentPermissions)) {
                $superAdminRole->givePermissionTo($permission);
                $this->command->info("  ✓ Added: {$permission->name}");
                $added++;
            }
        }
        
        // Clear cache after
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $this->command->info("\n✅ Done!");
        $this->command->info("Added {$added} missing permissions to Superadmin");
        $this->command->info("Superadmin now has: " . $superAdminRole->permissions->count() . " permissions");
    }
}
