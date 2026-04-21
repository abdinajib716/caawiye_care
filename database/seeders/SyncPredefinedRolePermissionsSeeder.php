<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\PermissionService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncPredefinedRolePermissionsSeeder extends Seeder
{
    public function __construct(
        private readonly PermissionService $permissionService
    ) {
    }

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->permissionService->createPermissions();

        $allPermissionNames = collect($this->permissionService->getAllPermissions())
            ->pluck('permissions')
            ->flatten()
            ->unique()
            ->values()
            ->all();

        $this->syncRolePermissions('Superadmin', $allPermissionNames);

        $adminExcludedPermissions = [
            'user.delete',
            'user.login_as',
        ];

        $this->syncRolePermissions('Admin', array_values(array_diff($allPermissionNames, $adminExcludedPermissions)));

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected function syncRolePermissions(string $roleName, array $permissionNames): void
    {
        $role = Role::where('name', $roleName)->first();

        if (! $role) {
            return;
        }

        $permissions = Permission::query()
            ->whereIn('name', $permissionNames)
            ->get();

        foreach ($permissions as $permission) {
            if (! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }
    }
}
