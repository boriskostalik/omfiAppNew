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
            'entered_by' => 1,
            'year' => $this->faker->year(),
            'actualyear' => $this->faker->year(),
            'title' => $this->faker->sentence(6),
            'title_eng' => $this->faker->sentence(6),
            'bibtex_id' => Str::random(10),
            'pub_type' => 'Article',
            'type' => 'Article',
            'survey' => 0,
            'mark' => 5,
            'series' => '',
            'journal' => $this->faker->company(),
            'url' => $this->faker->url(),
            'doi' => Str::random(10),
            'note' => $this->faker->text(20),
            'keywords' => implode(', ', $this->faker->words(3)), 
            'abstract' => $this->faker->text(100),
            'crossref' => Str::random(10),
            'namekey' => Str::random(10),
            'userfields' => '',

        ];
    }
}
