<?php

namespace App\Livewire\Atasan;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Models\Team;
use App\Models\Workspace;
use Livewire\Attributes\Layout;

#[Layout('layouts.atasan')]
class AtasanDashboard extends Component
{
    public $selectedPmId = null;

    public function selectPm($pmId)
    {
        $this->selectedPmId = $pmId;
    }

    public function render()
    {
        $userId = auth()->id();

        $total = Task::where('created_by', $userId)->count();
        $given = Task::where('created_by', $userId)->whereNotNull('assigned_to')->count();
        $pending = Task::where('created_by', $userId)->whereNull('assigned_to')->count();
        $done = Task::where('created_by', $userId)->where('status', 'done')->count();
        $belumSelesai = $total - $done;
        $deadlineCount = Task::where('created_by', $userId)
            ->whereNotNull('deadline')
            ->where('status', '!=', 'done')
            ->count();

        $chartData = [
            ['label' => 'Belum Selesai', 'count' => $belumSelesai, 'bg' => '#6366f1'],
            ['label' => 'Selesai', 'count' => $done, 'bg' => '#22c55e'],
            ['label' => 'Deadline', 'count' => $deadlineCount, 'bg' => '#f43f5e'],
        ];

        $pms = User::where('role', 'pm')
            ->with(['workspace', 'teams.members.user'])
            ->get()
            ->map(fn($pm) => [
                'id' => $pm->id,
                'name' => $pm->name,
                'email' => $pm->email,
                'phone' => $pm->phone,
                'workspace' => $pm->workspace,
                'teams' => $pm->teams->map(fn($team) => [
                    'id' => $team->id,
                    'name' => $team->name,
                    'member_count' => $team->members->count(),
                    'members' => $team->members->map(fn($m) => [
                        'name' => $m->user?->name ?? 'Unknown',
                        'role' => $m->role,
                    ]),
                ]),
            ]);

        $selectedPm = null;
        if ($this->selectedPmId) {
            $selectedPm = $pms->firstWhere('id', $this->selectedPmId);
        }

        return view('livewire.atasan.atasan-dashboard', [
            'total' => $total,
            'given' => $given,
            'pending' => $pending,
            'done' => $done,
            'deadlineCount' => $deadlineCount,
            'chartData' => $chartData,
            'pms' => $pms,
            'selectedPm' => $selectedPm,
        ]);
    }
}
