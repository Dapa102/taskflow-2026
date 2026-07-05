<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workspace;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TeamSeeder::class,
        ]);

        $pm = User::where('email', 'pm1@test.com')->first();
        $member1 = User::where('email', 'member1@test.com')->first();
        $member2 = User::where('email', 'member2@test.com')->first();

        if (!$pm || !$member1 || !$member2) return;

        $ws = Workspace::firstOrCreate(
            ['pm_id' => $pm->id],
            ['name' => 'Project Aplikasi', 'description' => 'Pengembangan aplikasi manajemen tugas']
        );
        $ws->members()->syncWithoutDetaching([$member1->id, $member2->id]);

        $tasks = [
            ['title' => 'Testing fitur notifikasi', 'desc' => 'Pastikan notifikasi berjalan', 'status' => 'pending_pm', 'priority' => 'medium', 'deadline' => 1, 'assign' => $member1->id],
            ['title' => 'Buat halaman profil user', 'desc' => 'Edit profil + avatar', 'status' => 'pending_admin', 'priority' => 'medium', 'deadline' => -1, 'assign' => $member2->id],
            ['title' => 'Implementasi dark mode', 'desc' => 'Toggle theme', 'status' => 'revision', 'priority' => 'low', 'deadline' => 3, 'assign' => $member1->id],
        ];

        foreach ($tasks as $t) {
            Task::firstOrCreate(
                ['title' => $t['title'], 'workspace_id' => $ws->id],
                [
                    'created_by' => $pm->id,
                    'assigned_to' => $t['assign'],
                    'assigned_member_id' => $t['assign'],
                    'assigned_pm_id' => $pm->id,
                    'description' => $t['desc'],
                    'status' => $t['status'],
                    'priority' => $t['priority'],
                    'deadline' => $t['deadline'] < 0 ? now()->subDays(abs($t['deadline'])) : now()->addDays($t['deadline']),
                ]
            );
        }
    }
}
