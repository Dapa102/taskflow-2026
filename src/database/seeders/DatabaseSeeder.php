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
            TeamSeeder::class,
            TaskSeeder::class,
        ]);

        $pm1 = User::where('email', 'pm1@test.com')->first();
        $pm2 = User::where('email', 'pm2@test.com')->first();
        $member1 = User::where('email', 'member1@test.com')->first();
        $member2 = User::where('email', 'member2@test.com')->first();

        $ws1 = Workspace::firstOrCreate(
            ['pm_id' => $pm1->id],
            ['name' => 'Alpha Team', 'description' => 'Project pengembangan aplikasi']
        );
        $ws1->members()->syncWithoutDetaching([$member1->id, $member2->id]);

        if ($pm2) {
            Workspace::firstOrCreate(
                ['pm_id' => $pm2->id],
                ['name' => 'Beta Team', 'description' => 'Project desain UI/UX']
            );
        }

        $tasks = [
            ['title' => 'Setup Frontend', 'desc' => 'Install React & Tailwind', 'status' => 'done', 'priority' => 'high', 'deadline' => -1, 'pm' => $pm1, 'assign' => $member1],
            ['title' => 'API Integration', 'desc' => 'Connect frontend to API', 'status' => 'on_progress', 'priority' => 'medium', 'deadline' => 2, 'pm' => $pm1, 'assign' => $member1],
            ['title' => 'Write Documentation', 'desc' => 'Update README', 'status' => 'todo', 'priority' => 'low', 'deadline' => -2, 'pm' => $pm1, 'assign' => $member2],
            ['title' => 'Penjadwalan Sprint', 'desc' => 'Jadwalkan sprint review', 'status' => 'todo', 'priority' => 'medium', 'deadline' => 5, 'pm' => $pm1, 'assign' => $member1],
            ['title' => 'Bug Report Dashboard', 'desc' => 'Filter tanggal tidak berfungsi', 'status' => 'on_progress', 'priority' => 'high', 'deadline' => 1, 'pm' => $pm1, 'assign' => $member2],
            ['title' => 'Code Review', 'desc' => 'Review PR modul auth', 'status' => 'todo', 'priority' => 'low', 'deadline' => 3, 'pm' => $pm1, 'assign' => $member1],
            ['title' => 'Deployment Staging', 'desc' => 'Deploy ke staging', 'status' => 'done', 'priority' => 'high', 'deadline' => -1, 'pm' => $pm1, 'assign' => $member2],
        ];

        foreach ($tasks as $t) {
            Task::firstOrCreate(
                ['title' => $t['title'], 'workspace_id' => $ws1->id],
                [
                    'created_by' => $t['pm']->id,
                    'assigned_to' => $t['assign']->id,
                    'description' => $t['desc'],
                    'status' => $t['status'],
                    'priority' => $t['priority'],
                    'deadline' => $t['deadline'] < 0 ? now()->subDays(abs($t['deadline'])) : now()->addDays($t['deadline']),
                ]
            );
        }
    }
}
