<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workspace;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        $workspace = Workspace::factory();
        return [
            'workspace_id' => $workspace,
            'created_by' => User::factory(['role' => 'pm']),
            'assigned_to' => User::factory(['role' => 'member']),
            'assigned_pm_id' => User::factory(['role' => 'pm']),
            'assigned_member_id' => User::factory(['role' => 'member']),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement([TaskStatus::TODO, TaskStatus::IN_PROGRESS, TaskStatus::DONE]),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'deadline' => fake()->optional(0.7)->dateTimeBetween('-1 week', '+1 month'),
        ];
    }

    public function todo(): static
    {
        return $this->state(fn () => ['status' => TaskStatus::TODO]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => TaskStatus::IN_PROGRESS]);
    }

    public function done(): static
    {
        return $this->state(fn () => ['status' => TaskStatus::DONE]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'deadline' => now()->subDays(3),
            'status' => TaskStatus::TODO,
        ]);
    }
}
