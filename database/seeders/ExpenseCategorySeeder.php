<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Provider Transactions',
                'slug' => 'provider-transactions',
                'description' => 'Payments to service providers (hospitals, labs, pharmacies, doctors)',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Salaries & Wages',
                'slug' => 'salaries-wages',
                'description' => 'Employee salaries and wages',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Telecom / API Costs',
                'slug' => 'telecom-api-costs',
                'description' => 'SMS, API calls, and telecommunication expenses',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Refunds',
                'slug' => 'refunds',
                'description' => 'Customer refund payouts',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Transportation',
                'slug' => 'transportation',
                'description' => 'Delivery and transportation costs',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Office Expenses',
                'slug' => 'office-expenses',
                'description' => 'Office supplies, utilities, and maintenance',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'slug' => 'marketing',
                'description' => 'Advertising and marketing expenses',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Miscellaneous',
                'slug' => 'miscellaneous',
                'description' => 'Other miscellaneous expenses',
                'is_system' => true,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
