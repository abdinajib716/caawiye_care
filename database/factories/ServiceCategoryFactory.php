<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceCategory>
 */
class ServiceCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServiceCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'parent_id' => null,
            'sort_order' => $this->faker->numberBetween(0, 100),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the category is a child category.
     */
    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => ServiceCategory::factory(),
        ]);
    }

    /**
     * Create predefined business service categories.
     */
    public function businessCategories(): static
    {
        return $this->sequence(
            ['name' => 'Medical Services', 'description' => 'Healthcare and medical related services'],
            ['name' => 'Laboratory Tests', 'description' => 'Medical laboratory and diagnostic tests'],
            ['name' => 'Consultations', 'description' => 'Professional consultation services'],
            ['name' => 'Reports & Analysis', 'description' => 'Medical reports and analysis services'],
            ['name' => 'Emergency Services', 'description' => 'Urgent and emergency medical services'],
        );
    }
}
