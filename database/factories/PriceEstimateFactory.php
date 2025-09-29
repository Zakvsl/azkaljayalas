<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceEstimate>
 */
class PriceEstimateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'project_type' => fake()->randomElement(['kitchen', 'wardrobe', 'bathroom', 'office']),
            'material_type' => fake()->randomElement(['wood', 'aluminum', 'glass', 'steel']),
            'dimensions' => [
                'width' => fake()->numberBetween(100, 500),
                'height' => fake()->numberBetween(100, 300),
                'depth' => fake()->numberBetween(30, 100),
            ],
            'additional_features' => [
                'lighting' => fake()->boolean(),
                'handles' => fake()->randomElement(['basic', 'premium', 'luxury']),
                'finish' => fake()->randomElement(['matte', 'glossy', 'textured']),
            ],
            'estimated_price' => fake()->numberBetween(1000000, 10000000),
            'actual_price' => fake()->optional()->numberBetween(1000000, 10000000),
            'status' => fake()->randomElement(['pending', 'confirmed', 'rejected']),
            'notes' => fake()->optional()->text(),
        ];
    }

    /**
     * Indicate that the estimate is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'actual_price' => null,
        ]);
    }

    /**
     * Indicate that the estimate is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'actual_price' => fake()->numberBetween(1000000, 10000000),
        ]);
    }

    /**
     * Indicate that the estimate is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'actual_price' => null,
        ]);
    }
}