<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'filename' => fake()->word() . '.' . fake()->randomElement(['pdf', 'jpg', 'png', 'docx']),
            'file_path' => 'attachments/test/' . fake()->uuid() . '.pdf',
            'file_size' => fake()->numberBetween(10000, 5000000),
            'mime_type' => fake()->randomElement([
                'application/pdf', 'image/jpeg', 'image/png',
                'application/msword',
            ]),
        ];
    }
}
