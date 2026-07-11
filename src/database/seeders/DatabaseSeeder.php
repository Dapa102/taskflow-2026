<?php

namespace Database\Seeders;

use App\Enums\TaskStatus;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Project;
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

        $project = Project::firstOrCreate(
            ['workspace_id' => $ws->id, 'name' => 'MVP TaskFlow'],
            [
                'description' => 'Project demo untuk validasi alur MVP TaskFlow',
                'deadline' => now()->addMonth(),
                'created_by' => $pm->id,
            ]
        );

        $tasks = [
            ['title' => 'Siapkan struktur dashboard Member', 'desc' => 'Buat tampilan daftar task pribadi', 'status' => TaskStatus::TODO, 'priority' => 'medium', 'deadline' => 3, 'assign' => $member1->id],
            ['title' => 'Update status task demo', 'desc' => 'Validasi perubahan status task ke In Progress', 'status' => TaskStatus::IN_PROGRESS, 'priority' => 'medium', 'deadline' => 5, 'assign' => $member2->id],
            ['title' => 'Review lampiran hasil kerja', 'desc' => 'Task demo menunggu review Project Manager', 'status' => TaskStatus::REVIEW, 'priority' => 'low', 'deadline' => 7, 'assign' => $member1->id],
        ];

        foreach ($tasks as $t) {
            Task::firstOrCreate(
                ['title' => $t['title'], 'workspace_id' => $ws->id],
                [
                    'created_by' => $pm->id,
                    'assigned_to' => $t['assign'],
                    'assigned_member_id' => $t['assign'],
                    'assigned_pm_id' => $pm->id,
                    'project_id' => $project->id,
                    'description' => $t['desc'],
                    'status' => $t['status'],
                    'priority' => $t['priority'],
                    'deadline' => $t['deadline'] < 0 ? now()->subDays(abs($t['deadline'])) : now()->addDays($t['deadline']),
                ]
            );
        }
    }
}
