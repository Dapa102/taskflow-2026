<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['todo', 'on_progress', 'done']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'deadline' => fake()->optional(0.7)->dateTimeBetween('-1 week', '+1 month'),
        ];
    }

    public function todo(): static
    {
        return $this->state(fn () => ['status' => 'todo']);
    }

    public function onProgress(): static
    {
        return $this->state(fn () => ['status' => 'on_progress']);
    }

    public function done(): static
    {
        return $this->state(fn () => ['status' => 'done']);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'deadline' => now()->subDays(3),
            'status' => 'todo',
        ]);
    }
}
