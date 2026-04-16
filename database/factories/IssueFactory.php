<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Issue>
 */
class IssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'year'   => $this->faker->numberBetween(2000, 2024),
            'volume' => (string) $this->faker->numberBetween(30, 55),
            'number' => $this->faker->numberBetween(1, 4),
        ];
    }
}
