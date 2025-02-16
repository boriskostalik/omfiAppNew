<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'surname' => $this->faker->lastName(),
            'von' => '',
            'firstname' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'url' => $this->faker->url(),
            'institute' => $this->faker->company(),
            'specialchars' => 'FALSE',
            'cleanname' => '',
        ];
    }
}
