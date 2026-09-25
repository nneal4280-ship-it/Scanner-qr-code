<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SiteFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->company(), 'latitude' => fake()->latitude(), 'longitude' => fake()->longitude(), 'radius_meters' => 100, 'is_active' => true];
    }
}
