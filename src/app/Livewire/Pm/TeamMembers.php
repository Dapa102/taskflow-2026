<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Task;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class TeamMembers extends Component
{
    public $inviteEmail = '';

    protected $rules = [
        'inviteEmail' => 'required|email|exists:users,email',
    ];

    public function inviteMember()
    {
        $this->validate();

        $workspace = auth()->user()->currentWorkspace();
        if (!$workspace) {
            session()->flash('error', 'Create a workspace first.');
            return;
        }

        $user = User::where('email', $this->inviteEmail)->where('role', 'member')->first();
        if (!$user) {
            session()->flash('error', 'User not found or is not a member role.');
            return;
        }

        if ($workspace->members()->where('user_id', $user->id)->exists()) {
            session()->flash('error', 'User is already in your workspace.');
            return;
        }

        $workspace->members()->attach($user->id);
        session()->flash('message', 'Member added successfully.');
        $this->reset('inviteEmail');
    }

    public function removeMember($userId)
    {
        $workspace = auth()->user()->currentWorkspace();
        if ($workspace) {
            $workspace->members()->detach($userId);
            session()->flash('message', 'Member removed.');
        }
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace();
        $members = $workspace ? $workspace->members()->latest()->get() : collect();

        $memberWorkload = $members->map(fn($m) => [
            'user' => $m,
            'active_tasks' => Task::where('assigned_member_id', $m->id)
                ->whereNotIn('status', ['done', 'cancelled'])->count(),
        ]);

        return view('livewire.pm.team-members', [
            'workspace' => $workspace,
            'members' => $members,
            'memberWorkload' => $memberWorkload,
        ]);
    }
}
