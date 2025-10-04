<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class HospitalPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define hospital permissions
        $permissions = [
            'hospital.view',
            'hospital.create',
            'hospital.edit',
            'hospital.delete',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all hospital permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $permissionIds = Permission::whereIn('name', $permissions)->pluck('id')->toArray();
            $superAdminRole->permissions()->syncWithoutDetaching($permissionIds);
        }

        $this->command->info('Hospital permissions created and assigned to Super Admin role.');
    }
}

