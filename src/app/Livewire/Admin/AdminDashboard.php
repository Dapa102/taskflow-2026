<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Task;
use App\Models\Team;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class AdminDashboard extends Component
{
    public function toggleUserStatus($userId)
    {
        if (auth()->id() == $userId) {
            session()->flash('error', 'Cannot suspend yourself.');
            return;
        }

        $user = User::find($userId);
        if ($user && $user->role !== 'admin') {
            $user->update(['is_active' => !$user->is_active]);
            session()->flash('message', 'User status updated.');
        }
    }

    public function render()
    {
        $taskStats = [
            'total' => Task::count(),
            'todo' => Task::where('status', 'todo')->count(),
            'on_progress' => Task::where('status', 'on_progress')->count(),
            'pending_pm' => Task::where('status', 'pending_pm')->count(),
            'pending_admin' => Task::where('status', 'pending_admin')->count(),
            'revision' => Task::where('status', 'revision')->count(),
            'done' => Task::where('status', 'done')->count(),
        ];

        $stats = [
            'users' => User::count(),
            'workspaces' => Workspace::count(),
            'tasks' => $taskStats,
        ];

        $users = User::latest()->get();
        $workspaces = Workspace::with('pm', 'tasks.assignee')->latest()->get();
        $teams = Team::with('owner', 'tasks.assignee', 'members.user')->latest()->get();
        $tasks = Task::with(['workspace', 'assignee', 'creator'])->latest()->get();

        return view('livewire.admin.admin-dashboard', [
            'stats' => $stats,
            'users' => $users,
            'workspaces' => $workspaces,
            'teams' => $teams,
            'tasks' => $tasks,
        ]);
    }
}
