<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@admin.com')->first();

        if (!$user) {
            return;
        }

        $teamDev = Team::where('name', 'Tim Developer')->first();
        $teamDesain = Team::where('name', 'Tim Desain')->first();

        $categories = [
            ['name' => 'Pekerjaan', 'color' => '#3B82F6'],
            ['name' => 'Kuliah', 'color' => '#10B981'],
            ['name' => 'Pribadi', 'color' => '#F59E0B'],
            ['name' => 'Proyek', 'color' => '#8B5CF6'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['name']] = Category::firstOrCreate(
                ['name' => $cat['name'], 'user_id' => $user->id],
                ['color' => $cat['color']]
            );
        }

        $tasks = [
            [
                'title' => 'Menyelesaikan laporan bulanan',
                'description' => 'Kompilasi data penjualan dan keuangan bulan ini untuk presentasi ke manajemen.',
                'status' => 'on_progress',
                'priority' => 'high',
                'deadline' => now()->addDays(2),
                'category' => 'Pekerjaan',
                'team' => $teamDev?->id,
                'subtasks' => ['Kumpulkan data penjualan', 'Buat grafik revenue', 'Tulis kesimpulan'],
                'comments' => ['Data dari tim finance sudah masuk, tinggal buat grafik.'],
            ],
            [
                'title' => 'Belajar Laravel Sanctum',
                'description' => 'Pelajari cara implementasi autentikasi API menggunakan Sanctum.',
                'status' => 'todo',
                'priority' => 'medium',
                'deadline' => now()->addDays(5),
                'category' => 'Kuliah',
                'subtasks' => ['Baca dokumentasi resmi', 'Buat project contoh'],
            ],
            [
                'title' => 'Review pull request tim',
                'description' => 'Review dan beri komentar pada 3 PR yang menunggu di GitHub.',
                'status' => 'todo',
                'priority' => 'high',
                'deadline' => now()->addDay(),
                'category' => 'Pekerjaan',
                'team' => $teamDev?->id,
            ],
            [
                'title' => 'Update dokumentasi API',
                'description' => 'Perbarui dokumentasi endpoint API yang baru ditambahkan.',
                'status' => 'done',
                'priority' => 'low',
                'deadline' => now()->subDays(1),
                'category' => 'Proyek',
                'subtasks' => ['Dokumentasi endpoint auth', 'Dokumentasi endpoint tasks', 'Review final'],
                'comments' => ['Semua endpoint sudah terdokumentasi.'],
            ],
            [
                'title' => 'Meeting dengan klien',
                'description' => 'Diskusi requirement baru untuk fitur dashboard analytics.',
                'status' => 'todo',
                'priority' => 'high',
                'deadline' => now()->addDays(3),
                'category' => 'Pekerjaan',
            ],
            [
                'title' => 'Refactor module autentikasi',
                'description' => 'Rapikan kode modul autentikasi agar lebih maintainable.',
                'status' => 'on_progress',
                'priority' => 'medium',
                'deadline' => now()->addDays(7),
                'category' => 'Proyek',
                'subtasks' => ['Ekstrak logic ke service class', 'Tambahkan unit test', 'Update dokumentasi'],
            ],
            [
                'title' => 'Setup CI/CD pipeline',
                'description' => 'Konfigurasi GitHub Actions untuk automated testing dan deployment.',
                'status' => 'todo',
                'priority' => 'medium',
                'deadline' => now()->addDays(10),
                'category' => 'Proyek',
            ],
            [
                'title' => 'Fix bug filter tanggal',
                'description' => 'Filter tanggal di halaman laporan menampilkan data yang salah.',
                'status' => 'done',
                'priority' => 'high',
                'deadline' => now()->subDays(3),
                'category' => 'Pekerjaan',
                'team' => $teamDev?->id,
                'comments' => ['Bug disebabkan oleh timezone mismatch. Sudah diperbaiki.'],
            ],
            [
                'title' => 'Beli domain untuk project baru',
                'status' => 'todo',
                'priority' => 'low',
                'deadline' => null,
                'category' => 'Pribadi',
            ],
            [
                'title' => 'Optimasi query database',
                'description' => 'Query di halaman daftar pesanan terlalu lambat, perlu indexing.',
                'status' => 'on_progress',
                'priority' => 'high',
                'deadline' => now()->addDays(1),
                'category' => 'Pekerjaan',
                'team' => $teamDev?->id,
                'subtasks' => ['Identifikasi slow queries', 'Tambahkan index', 'Benchmark sebelum/sesudah'],
            ],
            [
                'title' => 'Desain mockup halaman profil',
                'description' => 'Buat wireframe dan mockup untuk halaman profil user yang baru.',
                'status' => 'todo',
                'priority' => 'medium',
                'deadline' => now()->addDays(14),
                'category' => 'Proyek',
                'team' => $teamDesain?->id,
            ],
            [
                'title' => 'Backup database production',
                'status' => 'done',
                'priority' => 'high',
                'deadline' => now()->subDays(2),
                'category' => 'Pekerjaan',
                'team' => $teamDev?->id,
            ],
        ];

        foreach ($tasks as $taskData) {
            $subtasksData = $taskData['subtasks'] ?? [];
            $commentsData = $taskData['comments'] ?? [];
            $categoryName = $taskData['category'] ?? null;
            $teamId = $taskData['team'] ?? null;
            unset($taskData['subtasks'], $taskData['comments'], $taskData['category'], $taskData['team']);

            $taskData['user_id'] = $user->id;
            $taskData['team_id'] = $teamId;
            if ($categoryName && isset($categoryModels[$categoryName])) {
                $taskData['category_id'] = $categoryModels[$categoryName]->id;
            }

            $task = Task::create($taskData);

            foreach ($subtasksData as $i => $subtaskTitle) {
                Subtask::create([
                    'task_id' => $task->id,
                    'title' => $subtaskTitle,
                    'is_completed' => $task->status === 'done' ? true : ($i === 0 && $task->status === 'on_progress'),
                ]);
            }

            foreach ($commentsData as $commentContent) {
                Comment::create([
                    'task_id' => $task->id,
                    'user_id' => $user->id,
                    'content' => $commentContent,
                ]);
            }
        }
    }
}
