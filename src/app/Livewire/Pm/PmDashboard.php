<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Task;
use App\Services\TaskStatusHistoryService;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.pm')]
class PmDashboard extends Component
{
    public $reviewNote;
    public $rejectTaskId;
    public $assignTaskId;
    public $assignMemberId;

    public $detailModal = false;
    public $detailTitle = '';
    public $detailTasks = [];

    public function assignToMember($taskId)
    {
        $this->validate(['assignMemberId' => 'required|exists:users,id']);

        $workspace = auth()->user()->currentWorkspace();
        if (!$workspace) return;

        $task = Task::where('assigned_pm_id', auth()->id())
            ->whereIn('status', ['assigned_pm', 'assigned_member'])
            ->findOrFail($taskId);

        if (!$workspace->members()->where('user_id', $this->assignMemberId)->exists()) {
            session()->flash('error', 'Member must be in your workspace.');
            return;
        }

        $task->update([
            'assigned_member_id' => $this->assignMemberId,
            'assigned_to' => $this->assignMemberId,
        ]);

        app(TaskStatusHistoryService::class)->transition(
            $task, 'assigned_member', "Ditugaskan ke {$this->assignMemberId}"
        );

        session()->flash('message', 'Task assigned to member.');
        $this->reset(['assignTaskId', 'assignMemberId']);
    }

    public function approveTask($taskId)
    {
        $workspace = auth()->user()->currentWorkspace();
        if (!$workspace) return;

        $task = Task::where('assigned_pm_id', auth()->id())->findOrFail($taskId);
        $task->update(['reviewed_by' => auth()->id()]);

        app(TaskStatusHistoryService::class)->transition(
            $task, 'pending_admin', 'Disetujui PM, menunggu approval admin'
        );

        session()->flash('message', 'Task approved and sent to admin.');
    }

    public function rejectTask($taskId)
    {
        $this->validate(['reviewNote' => 'required|string|min:3|max:1000']);

        $workspace = auth()->user()->currentWorkspace();
        if (!$workspace) return;

        $task = Task::where('assigned_pm_id', auth()->id())->findOrFail($taskId);

        $note = $this->reviewNote;
        $newCounter = $task->revision_counter + 1;

        $task->update([
            'review_note' => $note,
            'reviewed_by' => auth()->id(),
            'revision_counter' => $newCounter,
        ]);

        if ($task->isRevisiLocked() || $newCounter >= $task->max_revision_limit) {
            app(TaskStatusHistoryService::class)->transition(
                $task, 'pending_arbitration', "Batas revisi tercapai ({$newCounter}/{$task->max_revision_limit}): {$note}"
            );
            session()->flash('message', 'Batas revisi tercapai. Tugas dikirim ke arbitrase.');
        } else {
            app(TaskStatusHistoryService::class)->transition(
                $task, 'revision', "Revisi ({$newCounter}/{$task->max_revision_limit}): {$note}"
            );
            session()->flash('message', 'Task returned for revision.');
        }

        $this->reset(['reviewNote', 'rejectTaskId']);
    }

    public function showDetail($label)
    {
        $this->detailTitle = $label;
        $statuses = match ($label) {
            'Dikirim ke PM' => ['assigned_pm'],
            'Dikerjakan' => ['assigned_member'],
            'Menunggu Review' => ['pending_pm'],
            'Revisi' => ['revision'],
            'Arbitrase' => ['pending_arbitration'],
            'Selesai' => ['done'],
            default => [],
        };

        $this->detailTasks = Task::with(['workspace', 'assignedMember', 'creator'])
            ->where('assigned_pm_id', auth()->id())
            ->whereIn('status', $statuses)
            ->latest()
            ->get();

        $this->detailModal = true;
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace();
        $members = $workspace ? $workspace->members : collect();

        $tasks = $workspace
            ? Task::where('assigned_pm_id', auth()->id())
                ->with(['assignedMember', 'assignedPm', 'creator', 'reviewedBy', 'attachments'])
                ->latest()
                ->get()
            : collect();

        $incomingTasks = $tasks->where('status', 'assigned_pm');
        $activeTasks = $tasks->whereIn('status', ['assigned_member', 'pending_pm', 'revision']);
        $pendingReview = $tasks->where('status', 'pending_pm');
        $overdue = $tasks->filter(fn($t) => $t->isOverdue())->count();

        $total = $tasks->count();
        $done = $tasks->where('status', 'done')->count();
        $belumSelesai = $total - $done;
        $deadlineCount = $tasks->filter(fn($t) => $t->deadline && $t->status !== 'done')->count();

        // Member workload (F-19)
        $memberWorkload = $members->map(fn($m) => [
            'user' => $m,
            'active_tasks' => Task::where('assigned_member_id', $m->id)
                ->whereNotIn('status', ['done', 'cancelled'])->count(),
        ]);

        $stats = [
            'total' => $total,
            'done' => $done,
            'pending_review' => $pendingReview->count(),
            'revision' => $tasks->where('status', 'revision')->count(),
            'overdue' => $overdue,
            'incoming' => $incomingTasks->count(),
        ];

        $chartData = [
            ['label' => 'Dikirim ke PM', 'count' => $incomingTasks->count(), 'bg' => '#3b82f6'],
            ['label' => 'Dikerjakan', 'count' => $tasks->where('status', 'assigned_member')->count(), 'bg' => '#6366f1'],
            ['label' => 'Menunggu Review', 'count' => $pendingReview->count(), 'bg' => '#eab308'],
            ['label' => 'Revisi', 'count' => $tasks->where('status', 'revision')->count(), 'bg' => '#f97316'],
            ['label' => 'Arbitrase', 'count' => $tasks->where('status', 'pending_arbitration')->count(), 'bg' => '#ef4444'],
            ['label' => 'Selesai', 'count' => $done, 'bg' => '#22c55e'],
        ];

        $revisionLimitWarnings = $tasks->filter(fn($t) =>
            $t->status === 'revision' && $t->max_revision_limit > 0
            && $t->revision_counter >= $t->max_revision_limit - 1
        )->map(fn($t) => [
            'id' => $t->id,
            'title' => $t->title,
            'counter' => $t->revision_counter,
            'limit' => $t->max_revision_limit,
        ]);

        $doneTasks = Task::where('assigned_pm_id', auth()->id())
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

        return view('livewire.pm.pm-dashboard', [
            'workspace' => $workspace,
            'members' => $members,
            'tasks' => $tasks,
            'incomingTasks' => $incomingTasks,
            'activeTasks' => $activeTasks,
            'stats' => $stats,
            'pendingReview' => $pendingReview,
            'chartData' => $chartData,
            'dailyChartData' => $dailyChartData,
            'memberWorkload' => $memberWorkload,
            'revisionLimitWarnings' => $revisionLimitWarnings,
        ]);
    }
}
