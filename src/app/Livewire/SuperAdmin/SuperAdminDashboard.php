<?php

namespace App\Livewire\SuperAdmin;

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
    public $detailModal = false;
    public $detailTitle = '';
    public $detailTasks = [];

    public function selectPm($pmId)
    {
        $this->selectedPmId = $pmId;
    }

    public function showDetail($label)
    {
        $this->detailTitle = $label;
        $userId = auth()->id();
        $statuses = match ($label) {
            'Draft' => ['draft'],
            'Dikerjakan' => ['assigned_member', 'pending_pm', 'revision'],
            'Menunggu Approval' => ['pending_admin'],
            'Arbitrase' => ['pending_arbitration'],
            'Selesai' => ['done'],
            default => [],
        };

        $query = Task::with(['workspace', 'assignedPm', 'assignedMember', 'creator']);
        if (!empty($statuses)) {
            $query->whereIn('status', $statuses);
        }
        $this->detailTasks = $query
            ->where('created_by', $userId)
            ->latest()
            ->get();

        $this->detailModal = true;
    }

    public function render()
    {
        $userId = auth()->id();
        $baseQuery = Task::where('created_by', $userId);

        $total = (clone $baseQuery)->count();
        $draft = (clone $baseQuery)->where('status', 'draft')->count();
        $assignedPm = (clone $baseQuery)->where('status', 'assigned_pm')->count();
        $inProgress = (clone $baseQuery)->whereIn('status', ['assigned_member', 'pending_pm', 'revision'])->count();
        $pendingAdmin = (clone $baseQuery)->where('status', 'pending_admin')->count();
        $pendingArbitration = (clone $baseQuery)->where('status', 'pending_arbitration')->count();
        $done = (clone $baseQuery)->where('status', 'done')->count();
        $deadlineCount = (clone $baseQuery)
            ->whereNotNull('deadline')
            ->whereNotIn('status', ['done', 'cancelled'])
            ->count();

        $chartData = [
            ['label' => 'Draft', 'count' => $draft, 'bg' => '#9ca3af'],
            ['label' => 'Dikerjakan', 'count' => $inProgress, 'bg' => '#6366f1'],
            ['label' => 'Menunggu Approval', 'count' => $pendingAdmin, 'bg' => '#a855f7'],
            ['label' => 'Arbitrase', 'count' => $pendingArbitration, 'bg' => '#ef4444'],
            ['label' => 'Selesai', 'count' => $done, 'bg' => '#22c55e'],
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
                    ->whereNotIn('status', ['done', 'cancelled'])->count(),
                'pending_review' => Task::where('assigned_pm_id', $pm->id)
                    ->where('status', 'pending_pm')->count(),
                'overdue' => Task::where('assigned_pm_id', $pm->id)
                    ->whereNotIn('status', ['done', 'cancelled'])
                    ->whereNotNull('deadline')
                    ->where('deadline', '<', now())->count(),
            ]);

        $selectedPm = null;
        if ($this->selectedPmId) {
            $selectedPm = $pms->firstWhere('id', $this->selectedPmId);
        }

        $doneTasks = Task::where('created_by', $userId)
            ->where('status', 'done')
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
                'bg' => $date->isToday() ? '#6366f1' : '#a5b4fc',
            ];
        }

        return view('livewire.super-admin.super-admin-dashboard', [
            'total' => $total,
            'draft' => $draft,
            'assignedPm' => $assignedPm,
            'inProgress' => $inProgress,
            'pendingAdmin' => $pendingAdmin,
            'pendingArbitration' => $pendingArbitration,
            'done' => $done,
            'deadlineCount' => $deadlineCount,
            'chartData' => $chartData,
            'dailyChartData' => $dailyChartData,
            'pms' => $pms,
            'selectedPm' => $selectedPm,
        ]);
    }
}
