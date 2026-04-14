<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RestoreSuperadminPermissions extends Command
{
    protected $signature = 'permissions:restore-superadmin';
    
    protected $description = 'Restore all missing permissions to Superadmin role';

    public function handle()
    {
        $this->info('=================================');
        $this->info('Restoring Superadmin Permissions');
        $this->info('=================================');
        $this->newLine();

        // Get Superadmin role
        $superAdminRole = Role::where('name', 'Superadmin')->first();

        if (!$superAdminRole) {
            $this->error('❌ Superadmin role not found!');
            return 1;
        }

        // Get all permissions
        $allPermissions = Permission::all();
        $currentPermissions = $superAdminRole->permissions->pluck('name')->toArray();

        $this->info("Total permissions in system: {$allPermissions->count()}");
        $this->info("Superadmin currently has: " . count($currentPermissions) . " permissions");
        $this->newLine();

        // Find missing permissions
        $missingPermissions = [];
        foreach ($allPermissions as $permission) {
            if (!in_array($permission->name, $currentPermissions)) {
                $missingPermissions[] = $permission->name;
            }
        }

        if (empty($missingPermissions)) {
            $this->info('✅ No missing permissions! Superadmin has all permissions.');
            return 0;
        }

        $this->warn("Missing Permissions (" . count($missingPermissions) . "):");
        foreach ($missingPermissions as $perm) {
            $this->line("  ❌ {$perm}");
        }

        $this->newLine();
        $this->info('Adding missing permissions...');
        $this->newLine();

        $added = 0;
        foreach ($allPermissions as $permission) {
            if (!in_array($permission->name, $currentPermissions)) {
                $superAdminRole->givePermissionTo($permission);
                $this->line("  ✓ Added: {$permission->name}");
                $added++;
            }
        }

        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->newLine();
        $this->info('=================================');
        $this->info('✅ SUCCESS!');
        $this->info('=================================');
        $this->info("Added {$added} missing permissions");
        $this->info("Superadmin now has: " . $superAdminRole->fresh()->permissions->count() . " permissions");
        $this->newLine();
        $this->warn('⚠️  IMPORTANT: Users need to LOG OUT and LOG BACK IN to see changes!');
        $this->info('=================================');

        return 0;
    }
}
