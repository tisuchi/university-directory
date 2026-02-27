<?php

namespace Tisuchi\UniversityDirectory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Tisuchi\UniversityDirectory\Enums\UniversityType;
use Tisuchi\UniversityDirectory\Models\University;

class UniversityFactory extends Factory
{
    protected $model = University::class;

    public function definition(): array
    {
        $name = $this->faker->company().' University';

        return [
            'wikidata_id' => 'Q'.$this->faker->unique()->numberBetween(1000, 999999),
            'name' => $name,
            'short_name' => strtoupper($this->faker->lexify('???')),
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'country_code' => $this->faker->randomElement(['DE', 'US', 'GB', 'FR', 'JP']),
            'type' => $this->faker->randomElement(UniversityType::cases())->value,
            'official_website' => $this->faker->url(),
            'aliases' => [$this->faker->company()],
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'description' => $this->faker->optional(0.7)->sentence(15),
        ];
    }
}
