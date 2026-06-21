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

        $admin = User::where('email', 'admin@admin.com')->first();
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
            // Tasks created by admin → assigned to PM
            ['title' => 'Buat modul login', 'desc' => 'Integrasi Laravel Breeze', 'status' => 'done', 'priority' => 'high', 'deadline' => -2, 'assign' => $pm->id, 'creator' => $admin->id],
            ['title' => 'Desain dashboard utama', 'desc' => 'Wireframe & mockup', 'status' => 'todo', 'priority' => 'medium', 'deadline' => 5, 'assign' => $pm->id, 'creator' => $admin->id],
            ['title' => 'Siapkan server staging', 'desc' => 'Deploy VPS', 'status' => 'on_progress', 'priority' => 'high', 'deadline' => 2, 'assign' => $pm->id, 'creator' => $admin->id],
            ['title' => 'Testing fitur notifikasi', 'desc' => 'Pastikan notifikasi berjalan', 'status' => 'pending_pm', 'priority' => 'medium', 'deadline' => 1, 'assign' => $member1->id, 'creator' => $pm->id],
            ['title' => 'Buat halaman profil user', 'desc' => 'Edit profil + avatar', 'status' => 'pending_admin', 'priority' => 'medium', 'deadline' => -1, 'assign' => $member2->id, 'creator' => $pm->id],
            ['title' => 'Implementasi dark mode', 'desc' => 'Toggle theme', 'status' => 'revision', 'priority' => 'low', 'deadline' => 3, 'assign' => $member1->id, 'creator' => $pm->id],
            ['title' => 'Optimasi query database', 'desc' => 'Tambahkan index', 'status' => 'todo', 'priority' => 'high', 'deadline' => 7, 'assign' => $pm->id, 'creator' => $admin->id],
        ];

        foreach ($tasks as $t) {
            Task::firstOrCreate(
                ['title' => $t['title'], 'workspace_id' => $ws->id],
                [
                    'created_by' => $t['creator'],
                    'assigned_to' => $t['assign'],
                    'description' => $t['desc'],
                    'status' => $t['status'],
                    'priority' => $t['priority'],
                    'deadline' => $t['deadline'] < 0 ? now()->subDays(abs($t['deadline'])) : now()->addDays($t['deadline']),
                ]
            );
        }
    }
}
