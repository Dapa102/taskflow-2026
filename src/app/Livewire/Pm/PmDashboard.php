<?php

namespace App\Livewire\Pm;

use App\Enums\TaskStatus;
use Livewire\Component;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
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
    public $cancelTaskId;
    public $cancelNote;

    public $projectDetailModal = false;
    public $projectDetail = null;

    public function showProjectDetail($projectId)
    {
        $this->projectDetail = Project::with(['workspace', 'creator', 'tasks' => fn($q) => $q->withCount('attachments')])
            ->withCount('tasks')
            ->findOrFail($projectId);
        $this->projectDetail->done_count = Task::where('project_id', $projectId)
            ->where('status', TaskStatus::DONE)->count();
        $this->projectDetailModal = true;
    }

    public function assignToMember($taskId)
    {
        $this->validate(['assignMemberId' => 'required|exists:users,id']);

        $workspace = auth()->user()->currentWorkspace();
        if (!$workspace) return;

        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', TaskStatus::TODO)
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
            $task, TaskStatus::TODO, "Ditugaskan ke {$this->assignMemberId}"
        );

        session()->flash('message', 'Task assigned to member.');
        $this->reset(['assignTaskId', 'assignMemberId']);
    }

    public function approveTask($taskId)
    {
        $workspace = auth()->user()->currentWorkspace();
        if (!$workspace) return;

        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', TaskStatus::REVIEW)
            ->findOrFail($taskId);
        $task->update(['reviewed_by' => auth()->id()]);

        app(TaskStatusHistoryService::class)->transition(
            $task, TaskStatus::PENDING_ADMIN, 'Disetujui Project Manager, menunggu approval Super Admin'
        );

        session()->flash('message', 'Tugas disetujui, menunggu approval Super Admin.');
    }

    public function rejectTask($taskId)
    {
        $this->validate(['reviewNote' => 'required|string|min:3|max:1000']);

        $workspace = auth()->user()->currentWorkspace();
        if (!$workspace) return;

        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', TaskStatus::REVIEW)
            ->findOrFail($taskId);

        $note = $this->reviewNote;

        $newCounter = $task->revision_counter + 1;
        $task->update([
            'review_note' => $note,
            'reviewed_by' => auth()->id(),
            'revision_counter' => $newCounter,
        ]);

        if ($newCounter >= $task->max_revision_limit) {
            app(TaskStatusHistoryService::class)->transition(
                $task, TaskStatus::PENDING_ARBITRATION, "Batas revisi tercapai, masuk arbitrase: {$note} ({$newCounter}/{$task->max_revision_limit})"
            );
            session()->flash('message', 'Batas revisi tercapai. Task masuk arbitrase.');
        } else {
            app(TaskStatusHistoryService::class)->transition(
                $task, TaskStatus::IN_PROGRESS, "Dikembalikan untuk perbaikan: {$note} ({$newCounter}/{$task->max_revision_limit})"
            );
            session()->flash('message', 'Task returned for update.');
        }

        $this->reset(['reviewNote', 'rejectTaskId']);
    }

    public function requestArbitration($taskId)
    {
        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', TaskStatus::IN_PROGRESS)
            ->where('revision_counter', '>', 0)
            ->findOrFail($taskId);

        app(TaskStatusHistoryService::class)->transition(
            $task, TaskStatus::PENDING_ARBITRATION, 'Diminta arbitrase oleh Project Manager'
        );
        session()->flash('message', 'Task diajukan ke arbitrase.');
    }

    public function confirmCancel($taskId)
    {
        $this->cancelTaskId = $taskId;
        $this->cancelNote = '';
    }

    public function cancelTask($taskId)
    {
        $this->validate(['cancelNote' => 'required|string|min:3|max:1000']);

        $workspace = auth()->user()->currentWorkspace();
        if (!$workspace) return;

        $task = Task::where('assigned_pm_id', auth()->id())
            ->findOrFail($taskId);

        app(TaskStatusHistoryService::class)->transition(
            $task, TaskStatus::CANCELLED, "Dibatalkan: {$this->cancelNote}"
        );

        session()->flash('message', 'Tugas dibatalkan.');
        $this->reset(['cancelTaskId', 'cancelNote']);
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

        $incomingTasks = $tasks->where('status', TaskStatus::TODO);
        $activeTasks = $tasks->whereIn('status', [TaskStatus::TODO, TaskStatus::IN_PROGRESS, TaskStatus::REVIEW]);
        $pendingReview = $tasks->where('status', TaskStatus::REVIEW);
        $overdue = $tasks->filter(fn($t) => $t->isOverdue())->count();

        $total = $tasks->count();
        $done = $tasks->where('status', TaskStatus::DONE)->count();
        $belumSelesai = $total - $done;
        $deadlineCount = $tasks->filter(fn($t) => $t->deadline && $t->status !== TaskStatus::DONE)->count();

        // Member workload (F-19)
        $memberWorkload = $members->map(fn($m) => [
            'user' => $m,
            'active_tasks' => Task::where('assigned_member_id', $m->id)
                ->whereNotIn('status', ['done', 'cancelled'])->count(),
        ]);

        $stats = [
            'total' => $total,
            'done' => $done,
            'pending_pm' => $pendingReview->count(),
            'revision' => $tasks->where('status', TaskStatus::IN_PROGRESS)->count(),
            'overdue' => $overdue,
            'incoming' => $incomingTasks->count(),
        ];

        $incomingCount = $incomingTasks->count();
        $reviewCount = $pendingReview->count();
        $inProgressCount = $tasks->where('status', TaskStatus::IN_PROGRESS)->count();
        $lainnya = $total - ($incomingCount + $reviewCount + $inProgressCount + $done);

        $chartData = [
            ['label' => 'To Do', 'count' => $incomingCount, 'bg' => '#cbd5e1'],
            ['label' => 'In Progress', 'count' => $inProgressCount, 'bg' => '#94a3b8'],
            ['label' => 'Review', 'count' => $reviewCount, 'bg' => '#64748b'],
            ['label' => 'Done', 'count' => $done, 'bg' => '#1e293b'],
        ];

        if ($lainnya > 0) {
            $chartData[] = ['label' => 'Cancelled', 'count' => $lainnya, 'bg' => '#334155'];
        }

        $projects = $workspace ? $workspace->projects()->withCount('tasks')->get() : collect();
        $projectCount = $projects->count();
        $projectProgress = $projects->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'total' => $p->tasks_count,
            'done' => \App\Models\Task::where('project_id', $p->id)->where('status', TaskStatus::DONE)->count(),
            'percentage' => $p->tasks_count > 0 ? round((\App\Models\Task::where('project_id', $p->id)->where('status', TaskStatus::DONE)->count() / $p->tasks_count) * 100) : 0,
        ]);

        $revisionLimitWarnings = collect();

        $doneTasks = Task::where('assigned_pm_id', auth()->id())
            ->where('status', TaskStatus::DONE)
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
            'projectCount' => $projectCount,
            'projectProgress' => $projectProgress,
        ]);
    }
}
