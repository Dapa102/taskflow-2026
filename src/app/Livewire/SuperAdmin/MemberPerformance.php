<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\User;
use App\Models\Task;
use App\Models\Workspace;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.super-admin')]
class MemberPerformance extends Component
{
    public $workspaceFilter = '';
    public $startDate = '';
    public $endDate = '';

    public function exportPdf()
    {
        $this->js("window.location.href = '" . route('export.member-performance') . "'");
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
        $query = User::where('role', 'member');

        $members = $query->with('workspaces')->get();

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

        foreach ($members as $member) {
            $tasks = (clone $taskQuery)->where('assigned_member_id', $member->id)->get();

            $member->total_tasks = $tasks->count();
            $member->done_tasks = $tasks->where('status', 'done')->count();
            $member->in_progress = $tasks->where('status', 'in_progress')->count();
            $member->review_tasks = $tasks->where('status', 'review')->count();
            $member->overdue_tasks = $tasks->filter(fn($t) =>
                $t->deadline && $t->deadline < now() && $t->status !== 'done'
            )->count();
            $member->completion_rate = $member->total_tasks > 0
                ? round(($member->done_tasks / $member->total_tasks) * 100, 2)
                : 0;
        }

        return $members;
    }
}
