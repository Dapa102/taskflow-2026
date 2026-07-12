<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\User;
use App\Models\Task;
use App\Models\Workspace;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.super-admin')]
class PmPerformance extends Component
{
    public $workspaceFilter = '';
    public $startDate = '';
    public $endDate = '';

    public function exportPdf()
    {
        return redirect()->route('export.pm-performance');
    }

    public function render()
    {
        return view('livewire.super-admin.performa-pm', [
            'pms' => $this->getPerformance(),
            'workspaces' => Workspace::orderBy('name')->get(),
        ]);
    }

    private function getPerformance()
    {
        $query = User::where('role', 'pm')->with('workspaces');

        $pmsList = $query->get();

        $taskQuery = Task::query();
        if ($this->workspaceFilter) {
            $taskQuery->where('workspace_id', $this->workspaceFilter);
        }
        if ($this->startDate) {
            $taskQuery->whereDate('created_at', '>=', Carbon::parse($this->startDate));
        }
        if ($this->endDate) {
            $taskQuery->whereDate('created_at', '<=', Carbon::parse($this->endDate));
        }

        foreach ($pmsList as $pm) {
            $tasks = (clone $taskQuery)->where('assigned_pm_id', $pm->id)->get();

            $pm->total_tasks = $tasks->count();
            $pm->done_tasks = $tasks->where('status', 'done')->count();
            $pm->overdue_tasks = $tasks->filter(fn($t) =>
                $t->deadline && $t->deadline < now() && $t->status !== 'done'
            )->count();
            $pm->on_time_rate = $pm->total_tasks > 0
                ? round(($pm->done_tasks / $pm->total_tasks) * 100, 2)
                : 0;
        }

        return $pmsList;
    }
}
