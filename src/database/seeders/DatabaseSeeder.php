<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workspace;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Create PM 1
        $pm1 = User::create([
            'name' => 'Project Manager 1',
            'email' => 'pm1@test.com',
            'password' => Hash::make('password'),
            'role' => 'pm',
        ]);

        $workspace1 = Workspace::create([
            'pm_id' => $pm1->id,
            'name' => 'Alpha Team',
            'description' => 'First testing team',
        ]);

        // 3. Create Members for PM 1
        $member1 = User::create([
            'name' => 'Member One',
            'email' => 'member1@test.com',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);
        
        $member2 = User::create([
            'name' => 'Member Two',
            'email' => 'member2@test.com',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);

        $workspace1->members()->attach([$member1->id, $member2->id]);

        // 4. Create Tasks for PM 1
        Task::create([
            'workspace_id' => $workspace1->id,
            'created_by' => $pm1->id,
            'assigned_to' => $member1->id,
            'title' => 'Setup Frontend',
            'description' => 'Install React and Tailwind',
            'status' => 'done',
            'priority' => 'high',
            'deadline' => now()->subDays(1),
        ]);

        Task::create([
            'workspace_id' => $workspace1->id,
            'created_by' => $pm1->id,
            'assigned_to' => $member1->id,
            'title' => 'API Integration',
            'description' => 'Connect frontend to Laravel API',
            'status' => 'on_progress',
            'priority' => 'medium',
            'deadline' => now()->addDays(2),
        ]);

        Task::create([
            'workspace_id' => $workspace1->id,
            'created_by' => $pm1->id,
            'assigned_to' => $member2->id,
            'title' => 'Write Documentation',
            'description' => 'Update README',
            'status' => 'todo',
            'priority' => 'low',
            'deadline' => now()->subDays(2), // Overdue
        ]);

        // 5. Create PM 2 (No workspace yet)
        User::create([
            'name' => 'Project Manager 2',
            'email' => 'pm2@test.com',
            'password' => Hash::make('password'),
            'role' => 'pm',
        ]);
        
        // 6. Create Suspended User
        User::create([
            'name' => 'Bad User',
            'email' => 'bad@test.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'is_active' => false,
        ]);
    }
}
