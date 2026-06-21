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
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        // --- PRD Data (Workspaces, Members, Tasks) ---

        $pm1 = User::where('email', 'pm1@test.com')->first();
        if (!$pm1) {
            $pm1 = User::create([
                'name' => 'Project Manager 1',
                'email' => 'pm1@test.com',
                'password' => Hash::make('password'),
                'role' => 'pm',
            ]);
        }

        $workspace1 = Workspace::firstOrCreate(
            ['pm_id' => $pm1->id],
            ['name' => 'Alpha Team', 'description' => 'First testing team']
        );

        $member1 = User::firstOrCreate(
            ['email' => 'member1@test.com'],
            ['name' => 'Member One', 'password' => Hash::make('password'), 'role' => 'member']
        );
        $member2 = User::firstOrCreate(
            ['email' => 'member2@test.com'],
            ['name' => 'Member Two', 'password' => Hash::make('password'), 'role' => 'member']
        );

        if (!$workspace1->members()->where('user_id', $member1->id)->exists()) {
            $workspace1->members()->attach($member1->id);
        }
        if (!$workspace1->members()->where('user_id', $member2->id)->exists()) {
            $workspace1->members()->attach($member2->id);
        }

        Task::firstOrCreate(
            ['title' => 'Setup Frontend', 'workspace_id' => $workspace1->id],
            [
                'created_by' => $pm1->id, 'assigned_to' => $member1->id,
                'description' => 'Install React and Tailwind',
                'status' => 'done', 'priority' => 'high',
                'deadline' => now()->subDays(1),
            ]
        );
        Task::firstOrCreate(
            ['title' => 'API Integration', 'workspace_id' => $workspace1->id],
            [
                'created_by' => $pm1->id, 'assigned_to' => $member1->id,
                'description' => 'Connect frontend to Laravel API',
                'status' => 'on_progress', 'priority' => 'medium',
                'deadline' => now()->addDays(2),
            ]
        );
        Task::firstOrCreate(
            ['title' => 'Write Documentation', 'workspace_id' => $workspace1->id],
            [
                'created_by' => $pm1->id, 'assigned_to' => $member2->id,
                'description' => 'Update README',
                'status' => 'todo', 'priority' => 'low',
                'deadline' => now()->subDays(2),
            ]
        );

        User::firstOrCreate(
            ['email' => 'pm2@test.com'],
            ['name' => 'Project Manager 2', 'password' => Hash::make('password'), 'role' => 'pm']
        );

        User::firstOrCreate(
            ['email' => 'bad@test.com'],
            ['name' => 'Bad User', 'password' => Hash::make('password'), 'role' => 'member', 'is_active' => false]
        );

        // --- More PRD Tasks for visual density ---
        Task::firstOrCreate(
            ['title' => 'Penjadwalan Sprint', 'workspace_id' => $workspace1->id],
            [
                'created_by' => $pm1->id, 'assigned_to' => $member1->id,
                'description' => 'Jadwalkan sprint review dan retrospective',
                'status' => 'todo', 'priority' => 'medium',
                'deadline' => now()->addDays(5),
            ]
        );
        Task::firstOrCreate(
            ['title' => 'Bug Report Dashboard', 'workspace_id' => $workspace1->id],
            [
                'created_by' => $pm1->id, 'assigned_to' => $member2->id,
                'description' => 'Filter tanggal di dashboard tidak berfungsi',
                'status' => 'on_progress', 'priority' => 'high',
                'deadline' => now()->addDays(1),
            ]
        );
        Task::firstOrCreate(
            ['title' => 'Code Review', 'workspace_id' => $workspace1->id],
            [
                'created_by' => $pm1->id, 'assigned_to' => $member1->id,
                'description' => 'Review PR dari member untuk modul auth',
                'status' => 'todo', 'priority' => 'low',
                'deadline' => now()->addDays(3),
            ]
        );
        Task::firstOrCreate(
            ['title' => 'Deployment Staging', 'workspace_id' => $workspace1->id],
            [
                'created_by' => $pm1->id, 'assigned_to' => $member2->id,
                'description' => 'Deploy latest build ke server staging',
                'status' => 'done', 'priority' => 'high',
                'deadline' => now()->subDays(1),
            ]
        );
    }
}
