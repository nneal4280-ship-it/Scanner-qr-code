<?php

namespace Database\Factories;

use App\Enums\AttendanceType;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PointageFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'site_id' => Site::factory(), 'type' => fake()->randomElement(AttendanceType::cases()), 'occurred_at' => now(), 'latitude' => 3.848, 'longitude' => 11.502, 'within_geofence' => true];
    }
}
