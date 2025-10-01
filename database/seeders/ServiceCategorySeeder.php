<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main service categories
        $categories = [
            [
                'name' => 'Medical Services',
                'description' => 'Healthcare and medical related services',
                'sort_order' => 1,
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Laboratory Tests',
                        'description' => 'Medical laboratory and diagnostic tests',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Imaging Services',
                        'description' => 'Medical imaging and radiology services',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Consultations',
                'description' => 'Professional consultation services',
                'sort_order' => 2,
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'General Consultation',
                        'description' => 'General health consultation services',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Specialist Consultation',
                        'description' => 'Specialized medical consultation services',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Reports & Analysis',
                'description' => 'Medical reports and analysis services',
                'sort_order' => 3,
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Report Review',
                        'description' => 'Professional medical report review services',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Data Analysis',
                        'description' => 'Medical data analysis and interpretation',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Emergency Services',
                'description' => 'Urgent and emergency medical services',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Preventive Care',
                'description' => 'Preventive healthcare and wellness services',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = ServiceCategory::create($categoryData);

            // Create child categories if they exist
            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                $childData['is_active'] = true;
                ServiceCategory::create($childData);
            }
        }

        // Create some additional random categories for testing
        ServiceCategory::factory()->count(5)->create();
    }
}
