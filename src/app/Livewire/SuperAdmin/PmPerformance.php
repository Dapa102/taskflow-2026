<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\User;
use App\Models\Task;
use App\Models\Workspace;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.super-admin')]
class PmPerformance extends Component
{
    public $workspaceFilter = '';
    public $startDate = '';
    public $endDate = '';

    public function exportPdf()
    {
        $this->dispatch('download-pdf', url: route('export.pm-performance'));
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
        $pms = User::where('role', 'pm')
            ->where('is_active', true)
            ->get(['id', 'name', 'email']);

        $stats = DB::table('tasks')
            ->selectRaw('assigned_pm_id, COUNT(*) as total_tasks, SUM(CASE WHEN status = "done" THEN 1 ELSE 0 END) as done_tasks')
            ->whereNotNull('assigned_pm_id')
            ->when($this->workspaceFilter, fn($q) => $q->where('workspace_id', $this->workspaceFilter))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', Carbon::parse($this->startDate)))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', Carbon::parse($this->endDate)))
            ->groupBy('assigned_pm_id')
            ->get()
            ->keyBy('assigned_pm_id');

        $overdueStats = DB::table('tasks')
            ->selectRaw('assigned_pm_id, COUNT(*) as count')
            ->whereNotNull('assigned_pm_id')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->where('status', '!=', 'done')
            ->when($this->workspaceFilter, fn($q) => $q->where('workspace_id', $this->workspaceFilter))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', Carbon::parse($this->startDate)))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', Carbon::parse($this->endDate)))
            ->groupBy('assigned_pm_id')
            ->pluck('count', 'assigned_pm_id');

        foreach ($pms as $pm) {
            $s = $stats[$pm->id] ?? null;
            $total = $s ? (int) $s->total_tasks : 0;
            $pm->total_tasks = $total;
            $pm->done_tasks = $s ? (int) $s->done_tasks : 0;
            $pm->overdue_tasks = (int) ($overdueStats[$pm->id] ?? 0);
            $pm->on_time_rate = $total > 0 ? round(($pm->done_tasks / $total) * 100, 2) : 0;
        }

        return $pms;
    }
}
