<?php

namespace App\Livewire\SuperAdmin;

use App\Enums\TaskStatus;
use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.super-admin')]
class SuperAdminDashboard extends Component
{
    public $selectedPmId = null;
    public function selectPm($pmId)
    {
        $this->selectedPmId = $pmId;
    }

    public function render()
    {
        $baseQuery = Task::query();

        $total = (clone $baseQuery)->count();
        $draft = (clone $baseQuery)->where('status', TaskStatus::TODO)->count();
        $inProgress = (clone $baseQuery)->where('status', TaskStatus::IN_PROGRESS)->count();
        $review = (clone $baseQuery)->where('status', TaskStatus::REVIEW)->count();
        $pendingAdmin = (clone $baseQuery)->where('status', TaskStatus::PENDING_ADMIN)->count();
        $cancelled = (clone $baseQuery)->where('status', TaskStatus::CANCELLED)->count();
        $done = (clone $baseQuery)->where('status', TaskStatus::DONE)->count();
        $deadlineCount = (clone $baseQuery)
            ->whereNotNull('deadline')
            ->whereNotIn('status', ['done', 'cancelled'])
            ->count();

        $chartData = [
            ['label' => 'To Do', 'count' => $draft, 'bg' => '#cbd5e1'],
            ['label' => 'In Progress', 'count' => $inProgress, 'bg' => '#94a3b8'],
            ['label' => 'Review', 'count' => $review, 'bg' => '#64748b'],
            ['label' => 'Menunggu Approval', 'count' => $pendingAdmin, 'bg' => '#475569'],
            ['label' => 'Cancelled', 'count' => $cancelled, 'bg' => '#334155'],
            ['label' => 'Done', 'count' => $done, 'bg' => '#1e293b'],
        ];

        $pms = User::where('role', 'pm')
            ->with(['workspaces'])
            ->where('is_active', true)
            ->get()
            ->map(fn($pm) => [
                'id' => $pm->id,
                'name' => $pm->name,
                'email' => $pm->email,
                'phone' => $pm->phone,
                'workspace' => $pm->workspaces->first(),
                'workspace_count' => $pm->workspaces->count(),
                'active_tasks' => Task::where('assigned_pm_id', $pm->id)
                    ->whereNotIn('status', [TaskStatus::DONE, TaskStatus::CANCELLED])->count(),
            'pending_pm' => Task::where('assigned_pm_id', $pm->id)
                ->where('status', TaskStatus::REVIEW)->count(),
            'pending_admin' => Task::where('assigned_pm_id', $pm->id)
                ->where('status', TaskStatus::PENDING_ADMIN)->count(),
                'overdue' => Task::where('assigned_pm_id', $pm->id)
                    ->whereNotIn('status', [TaskStatus::DONE, TaskStatus::CANCELLED])
                    ->whereNotNull('deadline')
                    ->where('deadline', '<', now())->count(),
            ]);

        $selectedPm = null;
        if ($this->selectedPmId) {
            $selectedPm = $pms->firstWhere('id', $this->selectedPmId);
        }

        $doneTasks = Task::where('status', TaskStatus::DONE)
            ->where('updated_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->get()
            ->groupBy(fn($t) => $t->updated_at->format('Y-m-d'));

        $dailyChartData = [];
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $key = $date->format('Y-m-d');
            $count = isset($doneTasks[$key]) ? $doneTasks[$key]->count() : 0;
            $dailyChartData[] = [
                'label' => $dayNames[$date->dayOfWeek],
                'count' => $count,
                'bg' => $date->isToday() ? '#1e293b' : '#94a3b8',
            ];
        }

        $workspaceCount = \App\Models\Workspace::count();
        $projectCount = \App\Models\Project::count();
        $pmCount = User::where('role', 'pm')->count();
        $memberCount = User::where('role', 'member')->count();

        return view('livewire.super-admin.super-admin-dashboard', [
            'total' => $total,
            'draft' => $draft,
            'inProgress' => $inProgress,
            'review' => $review,
            'cancelled' => $cancelled,
            'pendingAdmin' => $pendingAdmin,
            'done' => $done,
            'deadlineCount' => $deadlineCount,
            'chartData' => $chartData,
            'dailyChartData' => $dailyChartData,
            'pms' => $pms,
            'selectedPm' => $selectedPm,
            'workspaceCount' => $workspaceCount,
            'projectCount' => $projectCount,
            'pmCount' => $pmCount,
            'memberCount' => $memberCount,
        ]);
    }
}
