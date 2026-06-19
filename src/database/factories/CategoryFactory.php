<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->randomElement([
                'Pekerjaan', 'Kuliah', 'Rumah Tangga', 'Pribadi', 'Proyek',
                'Belanja', 'Kesehatan', 'Olahraga', 'Hobi', 'Lainnya',
            ]),
            'color' => fake()->randomElement([
                '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
                '#EC4899', '#06B6D4', '#F97316',
            ]),
        ];
    }
}
