<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FinancialPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Expense permissions
            'expense.view',
            'expense.create',
            'expense.edit',
            'expense.delete',
            'expense.approve',

            // Expense Category permissions
            'expense_category.view',
            'expense_category.create',
            'expense_category.edit',
            'expense_category.delete',

            // Refund permissions
            'refund.view',
            'refund.create',
            'refund.approve',
            'refund.process',

            // Provider Payment permissions
            'provider_payment.view',
            'provider_payment.create',
            'provider_payment.approve',
            'provider_payment.pay',

            // Report permissions
            'report.view',
            'report.export',

            // Collection permissions
            'collection.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign all permissions to Superadmin role
        $superAdminRole = Role::where('name', 'Superadmin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }

        // Assign view and basic permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminPermissions = [
                'expense.view',
                'expense.create',
                'expense.edit',
                'expense_category.view',
                'refund.view',
                'refund.create',
                'provider_payment.view',
                'report.view',
                'collection.view',
            ];
            $adminRole->givePermissionTo($adminPermissions);
        }

        // Assign view permissions to agent role
        $agentRole = Role::where('name', 'agent')->first();
        if ($agentRole) {
            $agentPermissions = [
                'expense.view',
                'expense.create',
                'refund.view',
                'collection.view',
            ];
            $agentRole->givePermissionTo($agentPermissions);
        }
    }
}
