<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Institution;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Publication>
 */
class PublicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'issue_id'  => \App\Models\Issue::factory(),
            'title'     => $this->faker->sentence(6),
            'title_eng' => $this->faker->sentence(6),
            'type'      => 'Article',
            'firstpage' => (string) $this->faker->numberBetween(1, 50),
            'lastpage'  => (string) $this->faker->numberBetween(51, 100),
            'doi'       => Str::random(10),
            'abstract'  => $this->faker->text(100),
        ];
    }
}
