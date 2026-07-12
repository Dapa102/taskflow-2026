<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\Task;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class Workspaces extends Component
{
    public $selectedWorkspace = null;
    public $showDetailModal = false;

    public function viewDetail($workspaceId)
    {
        $this->selectedWorkspace = Workspace::with('pm', 'deputyPm', 'members')->findOrFail($workspaceId);
        $this->showDetailModal = true;
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

        return view('livewire.pm.workspaces', [
            'workspaces' => $workspaces,
        ]);
    }
}
