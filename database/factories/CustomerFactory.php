<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $countryCodes = ['+252', '+254', '+251', '+256', '+255', '+1', '+44', '+971'];

        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->numerify('##-###-####'),
            'country_code' => $this->faker->randomElement($countryCodes),
            'address' => $this->faker->optional(0.7)->address(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
