<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Models\InboxNotification;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class Workspaces extends Component
{
    public $selectedWorkspace = null;
    public $showDetailModal = false;
    public $selectedTeamId = '';

    public function viewDetail($workspaceId)
    {
        $this->selectedWorkspace = Workspace::with('pm', 'deputyPm', 'members')->findOrFail($workspaceId);
        $this->selectedTeamId = '';
        $this->showDetailModal = true;
    }

    public function addTeam()
    {
        $this->validate(['selectedTeamId' => 'required|exists:teams,id']);

        $team = Team::with('members.user')->findOrFail($this->selectedTeamId);
        if (!$this->selectedWorkspace) return;

        $count = 0;
        foreach ($team->members as $tm) {
            $user = $tm->user;
            if ($user && !$this->selectedWorkspace->members()->where('user_id', $user->id)->exists()) {
                $this->selectedWorkspace->members()->attach($user->id);
                InboxNotification::create([
                    'user_id' => $user->id,
                    'subject' => 'Ditambahkan ke Workspace',
                    'message' => "Anda ditambahkan ke workspace \"{$this->selectedWorkspace->name}\" melalui tim \"{$team->name}\".",
                    'channel' => 'inbox',
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
                $count++;
            }
        }

        session()->flash('message', "{$count} anggota dari tim \"{$team->name}\" ditambahkan ke workspace.");
        $this->selectedWorkspace = $this->selectedWorkspace->fresh()->load('pm', 'deputyPm', 'members');
        $this->selectedTeamId = '';
    }

    public function render()
    {
        $userId = auth()->id();

        $workspaces = Workspace::with(['pm', 'deputyPm', 'members'])
            ->where(function ($q) use ($userId) {
                $q->where('pm_id', $userId)
                  ->orWhere('deputy_pm_id', $userId)
                  ->orWhereHas('members', fn($q) => $q->where('user_id', $userId));
            })
            ->get()
            ->map(fn($ws) => [
                'id' => $ws->id,
                'name' => $ws->name,
                'description' => $ws->description,
                'pm' => $ws->pm,
                'deputy_pm' => $ws->deputyPm,
                'member_count' => $ws->members->count(),
                'task_count' => Task::where('workspace_id', $ws->id)->count(),
            ]);

        $doneTasks = $this->selectedWorkspace
            ? Task::with('assignedMember')
                ->where('workspace_id', $this->selectedWorkspace->id)
                ->where('status', 'done')
                ->latest()
                ->get()
            : collect();

        $teams = Team::withCount('members')->where('owner_id', $userId)
            ->orWhereHas('members', fn($q) => $q->where('user_id', $userId))
            ->orderBy('name')->get();

        return view('livewire.pm.workspaces', [
            'workspaces' => $workspaces,
            'doneTasks' => $doneTasks,
            'teams' => $teams,
        ]);
    }
}
