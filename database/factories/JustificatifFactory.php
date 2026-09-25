<?php

namespace Database\Factories;

use App\Enums\AbsenceStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JustificatifFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'status' => AbsenceStatus::Pending, 'reason' => fake()->sentence(), 'starts_on' => today(), 'ends_on' => today(), 'submitted_at' => now()];
    }
}
