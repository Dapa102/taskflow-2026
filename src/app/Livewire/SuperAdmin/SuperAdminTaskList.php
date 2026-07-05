<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use App\Services\TaskStatusHistoryService;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.super-admin')]
class SuperAdminTaskList extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $search = '';
    public $showHistoryModal = false;
    public $historyTaskId = null;
    public $cancelNote = '';
    public $showCancelModal = false;
    public $cancelTaskId = null;
    public $arbitrationNote = '';
    public $showArbitrationModal = false;
    public $arbitrationTaskId = null;
    public $arbitrationAction = null;
    public $escalatedFilter = false;
    public $showReassignModal = false;
    public $reassignTaskId = null;
    public $reassignPmId = null;

    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public function viewHistory($taskId)
    {
        $this->historyTaskId = $taskId;
        $this->showHistoryModal = true;
    }

    public function confirmCancel($taskId)
    {
        $this->cancelTaskId = $taskId;
        $this->cancelNote = '';
        $this->showCancelModal = true;
    }

    public function cancelTask()
    {
        $this->validate(['cancelNote' => 'nullable|max:500']);

        $task = Task::findOrFail($this->cancelTaskId);

        if (in_array($task->status, ['done', 'cancelled'])) {
            session()->flash('error', 'Tugas sudah selesai atau dibatalkan.');
            $this->showCancelModal = false;
            return;
        }

        app(TaskStatusHistoryService::class)->transition(
            $task, 'cancelled', $this->cancelNote ?: 'Dibatalkan oleh Super Admin'
        );

        session()->flash('message', 'Tugas berhasil dibatalkan.');
        $this->showCancelModal = false;
        $this->cancelTaskId = null;
    }

    public function approveTask($taskId)
    {
        $task = Task::where('status', 'pending_admin')->findOrFail($taskId);

        app(TaskStatusHistoryService::class)->transition(
            $task, 'done', 'Disetujui oleh Super Admin'
        );

        session()->flash('message', 'Tugas selesai disetujui.');
    }

    public function confirmArbitration($taskId, $action)
    {
        $task = Task::where('status', 'pending_arbitration')->findOrFail($taskId);

        $this->arbitrationTaskId = $taskId;
        $this->arbitrationAction = $action;
        $this->arbitrationNote = '';
        $this->showArbitrationModal = true;
    }

    public function executeArbitration()
    {
        $task = Task::where('status', 'pending_arbitration')->findOrFail($this->arbitrationTaskId);

        if ($this->arbitrationAction === 'approve') {
            app(TaskStatusHistoryService::class)->transition(
                $task, 'pending_admin', 'Arbitrase: disetujui Super Admin'
            );
            session()->flash('message', 'Arbitrase disetujui. Tugas masuk antrean approval admin.');
        } else {
            $note = $this->arbitrationNote ?: 'Dikembalikan ke revisi oleh Super Admin';
            $newCounter = $task->revision_counter + 1;
            $task->update([
                'review_note' => $note,
                'revision_counter' => $newCounter,
            ]);
            app(TaskStatusHistoryService::class)->transition(
                $task, 'revision', "Arbitrase: {$note} ({$newCounter}/{$task->max_revision_limit})"
            );
            session()->flash('message', 'Arbitrase: tugas dikembalikan ke revisi.');
        }

        $this->reset(['arbitrationTaskId', 'arbitrationAction', 'arbitrationNote', 'showArbitrationModal']);
    }

    public function approveEscalatedTask($taskId)
    {
        $task = Task::where('status', 'pending_pm')->whereNotNull('escalated_at')->findOrFail($taskId);

        app(TaskStatusHistoryService::class)->transition(
            $task, 'done', 'Eskalasi: disetujui langsung oleh Super Admin'
        );

        session()->flash('message', 'Tugas eskalasi disetujui (bypass PM).');
    }

    public function rejectEscalatedTask($taskId)
    {
        $task = Task::where('status', 'pending_pm')->whereNotNull('escalated_at')->findOrFail($taskId);

        $newCounter = $task->revision_counter + 1;
        $task->update([
            'review_note' => 'Ditolak via eskalasi oleh Super Admin',
            'revision_counter' => $newCounter,
        ]);

        app(TaskStatusHistoryService::class)->transition(
            $task, 'revision', "Eskalasi: ditolak Super Admin, kembali ke revisi ({$newCounter}/{$task->max_revision_limit})"
        );

        session()->flash('message', 'Tugas eskalasi dikembalikan ke revisi.');
    }

    public function confirmReassign($taskId)
    {
        $this->reassignTaskId = $taskId;
        $this->reassignPmId = null;
        $this->showReassignModal = true;
    }

    public function reassignPm()
    {
        $this->validate(['reassignPmId' => 'required|exists:users,id']);

        $task = Task::whereNotNull('escalated_at')->findOrFail($this->reassignTaskId);

        $oldPm = $task->assignedPm?->name ?? 'unknown';

        $task->update([
            'assigned_pm_id' => $this->reassignPmId,
            'escalated_at' => null,
        ]);

        app(TaskStatusHistoryService::class)->transition(
            $task, 'assigned_pm', "Eskalasi: dipindahkan dari {$oldPm} ke PM #{$this->reassignPmId}"
        );

        session()->flash('message', 'Tugas dipindahkan ke PM baru.');
        $this->reset(['reassignTaskId', 'reassignPmId', 'showReassignModal']);
    }

    public function render()
    {
        $query = Task::with(['workspace', 'assignee', 'assignedPm', 'assignedMember', 'attachments.user']);

        if ($this->escalatedFilter) {
            $query->whereNotNull('escalated_at');
        }

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $tasks = $query->latest()->paginate(20);

        $history = null;
        if ($this->historyTaskId) {
            $history = TaskStatusHistory::with('changer')
                ->where('task_id', $this->historyTaskId)
                ->orderBy('created_at')
                ->get();
        }

        return view('livewire.super-admin.super-admin-task-list', [
            'tasks' => $tasks,
            'history' => $history,
        ]);
    }

    public function getStatusLabel(string $status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'assigned_pm' => 'Dikirim ke PM',
            'assigned_member' => 'Dikerjakan Anggota',
            'pending_pm' => 'Menunggu Review PM',
            'revision' => 'Revisi',
            'pending_arbitration' => 'Arbitrase',
            'pending_admin' => 'Menunggu Approval',
            'done' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $status,
        };
    }

    public function getStatusColor(string $status): string
    {
        return match ($status) {
            'draft' => 'gray',
            'assigned_pm' => 'blue',
            'assigned_member' => 'indigo',
            'pending_pm' => 'yellow',
            'revision' => 'orange',
            'pending_arbitration' => 'red',
            'pending_admin' => 'purple',
            'done' => 'green',
            'cancelled' => 'slate',
            default => 'gray',
        };
    }
}
