<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Task;
use App\Services\TaskStatusHistoryService;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class PmDashboard extends Component
{
    public $workspaceName;
    public $workspaceDesc;

    public $inviteEmail;

    public $reviewNote;
    public $rejectTaskId;
    public $assignTaskId;
    public $assignMemberId;

    public function createWorkspace()
    {
        $this->validate([
            'workspaceName' => 'required|string|max:100',
            'workspaceDesc' => 'nullable|string',
        ]);

        if (auth()->user()->workspace) {
            session()->flash('error', 'You already have a workspace.');
            return;
        }

        Workspace::create([
            'pm_id' => auth()->id(),
            'name' => $this->workspaceName,
            'description' => $this->workspaceDesc,
        ]);

        session()->flash('message', 'Workspace created successfully.');
        $this->reset(['workspaceName', 'workspaceDesc']);
    }

    public function inviteMember()
    {
        $this->validate([
            'inviteEmail' => 'required|email|exists:users,email',
        ]);

        $workspace = auth()->user()->workspace;
        if (!$workspace) {
            session()->flash('error', 'Create a workspace first.');
            return;
        }

        $user = User::where('email', $this->inviteEmail)->where('role', 'member')->first();
        if (!$user) {
            session()->flash('error', 'User not found or is not a member role.');
            return;
        }

        if ($workspace->members()->where('user_id', $user->id)->exists()) {
            session()->flash('error', 'User is already in your workspace.');
            return;
        }

        $workspace->members()->attach($user->id);
        session()->flash('message', 'Member added successfully.');
        $this->reset('inviteEmail');
    }

    public function removeMember($userId)
    {
        $workspace = auth()->user()->workspace;
        if ($workspace) {
            $workspace->members()->detach($userId);
            session()->flash('message', 'Member removed.');
        }
    }

    public function assignToMember($taskId)
    {
        $this->validate(['assignMemberId' => 'required|exists:users,id']);

        $workspace = auth()->user()->workspace;
        if (!$workspace) return;

        $task = Task::where('assigned_pm_id', auth()->id())->findOrFail($taskId);

        if (!$workspace->members()->where('user_id', $this->assignMemberId)->exists()) {
            session()->flash('error', 'Member must be in your workspace.');
            return;
        }

        app(TaskStatusHistoryService::class)->transition(
            $task, 'assigned_member', "Ditugaskan ke {$this->assignMemberId}"
        );

        $task->update([
            'assigned_member_id' => $this->assignMemberId,
            'assigned_to' => $this->assignMemberId,
        ]);

        session()->flash('message', 'Task assigned to member.');
        $this->reset(['assignTaskId', 'assignMemberId']);
    }

    public function approveTask($taskId)
    {
        $workspace = auth()->user()->workspace;
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

        $workspace = auth()->user()->workspace;
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

    public function render()
    {
        $workspace = auth()->user()->workspace;
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

        return view('livewire.pm.pm-dashboard', [
            'workspace' => $workspace,
            'members' => $members,
            'tasks' => $tasks,
            'incomingTasks' => $incomingTasks,
            'activeTasks' => $activeTasks,
            'stats' => $stats,
            'pendingReview' => $pendingReview,
            'chartData' => $chartData,
            'memberWorkload' => $memberWorkload,
            'revisionLimitWarnings' => $revisionLimitWarnings,
        ]);
    }
}
