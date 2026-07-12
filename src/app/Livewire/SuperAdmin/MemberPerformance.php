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
class MemberPerformance extends Component
{
    public $workspaceFilter = '';
    public $startDate = '';
    public $endDate = '';

    public function exportPdf()
    {
        $this->dispatch('download-pdf', url: route('export.member-performance'));
    }

    public function render()
    {
        return view('livewire.super-admin.member-performance', [
            'members' => $this->getPerformance(),
            'workspaces' => Workspace::orderBy('name')->get(),
        ]);
    }

    private function getPerformance()
    {
        $members = User::where('role', 'member')
            ->where('is_active', true)
            ->get(['id', 'name', 'email']);

        $stats = DB::table('tasks')
            ->selectRaw("
                assigned_member_id,
                COUNT(*) as total_tasks,
                SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as done_tasks,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'review' THEN 1 ELSE 0 END) as review_tasks
            ")
            ->whereNotNull('assigned_member_id')
            ->when($this->workspaceFilter, fn($q) => $q->where('workspace_id', $this->workspaceFilter))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', Carbon::parse($this->startDate)))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', Carbon::parse($this->endDate)))
            ->groupBy('assigned_member_id')
            ->get()
            ->keyBy('assigned_member_id');

        $overdueStats = DB::table('tasks')
            ->selectRaw('assigned_member_id, COUNT(*) as count')
            ->whereNotNull('assigned_member_id')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->where('status', '!=', 'done')
            ->when($this->workspaceFilter, fn($q) => $q->where('workspace_id', $this->workspaceFilter))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', Carbon::parse($this->startDate)))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', Carbon::parse($this->endDate)))
            ->groupBy('assigned_member_id')
            ->pluck('count', 'assigned_member_id');

        foreach ($members as $member) {
            $s = $stats[$member->id] ?? null;
            $member->total_tasks = $s ? (int) $s->total_tasks : 0;
            $member->done_tasks = $s ? (int) $s->done_tasks : 0;
            $member->in_progress = $s ? (int) $s->in_progress : 0;
            $member->review_tasks = $s ? (int) $s->review_tasks : 0;
            $member->overdue_tasks = (int) ($overdueStats[$member->id] ?? 0);
            $member->completion_rate = $member->total_tasks > 0
                ? round(($member->done_tasks / $member->total_tasks) * 100, 2) : 0;
        }

        return $members;
    }
}
