<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Task;
use App\Models\Team;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
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
        $stats = [
            'users' => User::count(),
            'workspaces' => Workspace::count(),
            'tasks' => Task::count(),
        ];

        $users = User::latest()->get();
        $workspaces = Workspace::with('pm')->latest()->get();
        $teams = Team::with('owner')->latest()->get();

        return view('livewire.admin.admin-dashboard', [
            'stats' => $stats,
            'users' => $users,
            'workspaces' => $workspaces,
            'teams' => $teams,
        ]);
    }
}
