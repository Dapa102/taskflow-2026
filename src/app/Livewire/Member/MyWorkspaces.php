<?php

namespace App\Livewire\Member;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\Task;
use Livewire\Attributes\Layout;

#[Layout('layouts.member')]
class MyWorkspaces extends Component
{
    public $detailModal = false;
    public $wsDetail = null;

    public function showDetail($workspaceId)
    {
        $this->wsDetail = Workspace::with('pm', 'members')->findOrFail($workspaceId);
        $this->detailModal = true;
    }

    public function render()
    {
        $workspaces = auth()->user()->memberWorkspaces()
            ->with('pm')
            ->get()
            ->map(fn($ws) => [
                'id' => $ws->id,
                'name' => $ws->name,
                'description' => $ws->description,
                'pm' => $ws->pm,
                'member_count' => $ws->members->count(),
                'task_count' => Task::where('workspace_id', $ws->id)->count(),
            ]);

        return view('livewire.member.my-workspaces', [
            'workspaces' => $workspaces,
        ]);
    }
}
