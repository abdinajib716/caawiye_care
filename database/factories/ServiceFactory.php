<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        $price = $this->faker->randomFloat(2, 25, 500);
        $cost = $price * $this->faker->randomFloat(2, 0.3, 0.7); // Cost is 30-70% of price

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'short_description' => $this->faker->sentence(),
            'price' => $price,
            'cost' => $cost,
            'category_id' => ServiceCategory::factory(),
            'sku' => 'SRV-' . strtoupper($this->faker->bothify('???###')),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
            'meta_title' => $name,
            'meta_description' => $this->faker->sentence(),
        ];
    }

    /**
     * Indicate that the service is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the service is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the service is discontinued.
     */
    public function discontinued(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'discontinued',
        ]);
    }

    /**
     * Indicate that the service is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Create predefined medical services.
     */
    public function medicalServices(): static
    {
        return $this->sequence(
            [
                'name' => 'Blood Test Analysis',
                'slug' => 'blood-test-analysis',
                'description' => 'Comprehensive blood work analysis including CBC, chemistry panel, and lipid profile',
                'short_description' => 'Complete blood analysis with detailed report',
                'price' => 75.00,
                'cost' => 25.00,
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'Medical Report Review',
                'slug' => 'medical-report-review',
                'description' => 'Professional review and analysis of medical reports by qualified healthcare professionals',
                'short_description' => 'Expert medical report analysis and consultation',
                'price' => 150.00,
                'cost' => 50.00,
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'X-Ray Interpretation',
                'slug' => 'xray-interpretation',
                'description' => 'Professional interpretation of X-ray images with detailed findings report',
                'short_description' => 'Expert X-ray analysis and reporting',
                'price' => 120.00,
                'cost' => 40.00,
                'status' => 'active',
            ],
            [
                'name' => 'Health Consultation',
                'slug' => 'health-consultation',
                'description' => 'One-on-one health consultation with certified healthcare professionals',
                'short_description' => 'Personal health consultation session',
                'price' => 200.00,
                'cost' => 80.00,
                'status' => 'active',
            ],
            [
                'name' => 'Emergency Medical Review',
                'slug' => 'emergency-medical-review',
                'description' => 'Urgent medical report review for emergency cases with priority processing',
                'short_description' => 'Priority emergency medical consultation',
                'price' => 300.00,
                'cost' => 100.00,
                'status' => 'active',
                'is_featured' => true,
            ],
        );
    }
}
