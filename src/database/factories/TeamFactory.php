<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'name' => fake()->unique()->randomElement([
                'Proyek A', 'Tim Desain', 'Developer', 'Marketing',
                'Content Writer', 'Riset & Dev', 'Quality Assurance',
            ]),
            'invite_code' => strtoupper(fake()->bothify('??######')),
        ];
    }
}
