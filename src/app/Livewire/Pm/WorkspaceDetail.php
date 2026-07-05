<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\Task;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class WorkspaceDetail extends Component
{
    public function render()
    {
        $workspace = auth()->user()->currentWorkspace();

        $stats = $workspace ? [
            'total_tasks' => Task::where('assigned_pm_id', auth()->id())->count(),
            'active_tasks' => Task::where('assigned_pm_id', auth()->id())
                ->whereNotIn('status', ['done', 'cancelled'])->count(),
            'done_tasks' => Task::where('assigned_pm_id', auth()->id())
                ->where('status', 'done')->count(),
            'member_count' => $workspace->members()->count(),
        ] : null;

        return view('livewire.pm.workspace-detail', [
            'workspace' => $workspace,
            'stats' => $stats,
        ]);
    }
}
