<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Livewire\Attributes\Layout;

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
            ->with(['workspace'])
            ->where('is_active', true)
            ->get()
            ->map(fn($pm) => [
                'id' => $pm->id,
                'name' => $pm->name,
                'email' => $pm->email,
                'phone' => $pm->phone,
                'workspace' => $pm->workspace,
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
            'pms' => $pms,
            'selectedPm' => $selectedPm,
        ]);
    }
}
