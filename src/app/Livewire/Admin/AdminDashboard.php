<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Task;
use App\Models\Team;
use Livewire\Attributes\Layout;

#[Layout('layouts.super-admin')]
class AdminDashboard extends Component
{
    public function render()
    {
        $totalTasks = Task::count();
        $doneTasks = Task::where('status', 'done')->count();
        $belumSelesai = $totalTasks - $doneTasks;
        $deadlineCount = Task::whereNotNull('deadline')->where('status', '!=', 'done')->count();

        $taskStats = [
            'total' => $totalTasks,
            'todo' => Task::where('status', 'todo')->count(),
            'on_progress' => Task::where('status', 'on_progress')->count(),
            'pending_pm' => Task::where('status', 'pending_pm')->count(),
            'pending_admin' => Task::where('status', 'pending_admin')->count(),
            'revision' => Task::where('status', 'revision')->count(),
            'done' => $doneTasks,
        ];

        $stats = [
            'users' => User::count(),
            'workspaces' => Workspace::count(),
            'tasks' => $taskStats,
        ];

        $workspaces = Workspace::with('pm', 'tasks.assignee')->latest()->get();
        $teams = Team::with('owner', 'tasks.assignee', 'members.user')->latest()->get();
        $tasks = Task::with(['workspace', 'assignee', 'creator'])->latest()->get();

        $chartData = [
            ['label' => 'Belum Selesai', 'count' => $belumSelesai, 'bg' => '#6366f1'],
            ['label' => 'Selesai', 'count' => $doneTasks, 'bg' => '#22c55e'],
            ['label' => 'Deadline', 'count' => $deadlineCount, 'bg' => '#f43f5e'],
        ];

        return view('livewire.admin.admin-dashboard', [
            'stats' => $stats,
            'workspaces' => $workspaces,
            'teams' => $teams,
            'tasks' => $tasks,
            'chartData' => $chartData,
        ]);
    }
}
