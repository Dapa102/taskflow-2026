<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@admin.com')->first();
        if (!$user) return;

        $ws = Workspace::firstOrCreate(
            ['name' => 'Personal'],
            ['pm_id' => $user->id, 'description' => 'Tugas pribadi']
        );

        $tasks = [
            ['title' => 'Menyelesaikan laporan bulanan', 'status' => 'on_progress', 'priority' => 'high', 'deadline' => 2],
            ['title' => 'Belajar Laravel Sanctum', 'status' => 'todo', 'priority' => 'medium', 'deadline' => 5],
            ['title' => 'Update dokumentasi API', 'status' => 'done', 'priority' => 'low', 'deadline' => -1],
            ['title' => 'Meeting dengan klien', 'status' => 'todo', 'priority' => 'high', 'deadline' => 3],
            ['title' => 'Setup CI/CD pipeline', 'status' => 'todo', 'priority' => 'medium', 'deadline' => 10],
            ['title' => 'Beli domain project baru', 'status' => 'todo', 'priority' => 'low', 'deadline' => null],
        ];

        foreach ($tasks as $t) {
            Task::create([
                'workspace_id' => $ws->id,
                'created_by' => $user->id,
                'assigned_to' => $user->id,
                'title' => $t['title'],
                'status' => $t['status'],
                'priority' => $t['priority'],
                'deadline' => $t['deadline'] ? ($t['deadline'] < 0 ? now()->subDays(abs($t['deadline'])) : now()->addDays($t['deadline'])) : null,
            ]);
        }
    }
}
