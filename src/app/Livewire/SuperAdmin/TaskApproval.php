<?php

namespace App\Livewire\SuperAdmin;

use App\Enums\TaskStatus;
use Livewire\Component;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use App\Services\TaskStatusHistoryService;
use Livewire\Attributes\Layout;

#[Layout('layouts.super-admin')]
class TaskApproval extends Component
{
    public $search = '';
    public $showDetailModal = false;
    public $detailTask = null;
    public $rejectNote = '';
    public $showRejectModal = false;
    public $rejectTaskId = null;
    public $showHistoryModal = false;
    public $historyTaskId = null;

    public function updatingSearch() { $this->resetPage(); }

    public function viewDetail($taskId)
    {
        $this->detailTask = Task::with(['workspace', 'assignedPm', 'assignedMember', 'creator', 'attachments', 'project'])
            ->where('status', TaskStatus::PENDING_ADMIN)
            ->findOrFail($taskId);
        $this->showDetailModal = true;
    }

    public function viewHistory($taskId)
    {
        $this->historyTaskId = $taskId;
        $this->showHistoryModal = true;
    }

    public function approveFinal($taskId)
    {
        $task = Task::where('status', TaskStatus::PENDING_ADMIN)->findOrFail($taskId);

        app(TaskStatusHistoryService::class)->transition(
            $task, TaskStatus::DONE, 'Disetujui Super Admin (Final Approval)'
        );

        $this->showDetailModal = false;
        session()->flash('message', 'Tugas berhasil disetujui final.');
    }

    public function confirmReject($taskId)
    {
        $this->rejectTaskId = $taskId;
        $this->rejectNote = '';
        $this->showRejectModal = true;
    }

    public function rejectTask()
    {
        $this->validate(['rejectNote' => 'required|string|min:3|max:1000']);

        $task = Task::where('status', TaskStatus::PENDING_ADMIN)->findOrFail($this->rejectTaskId);

        $task->update(['review_note' => $this->rejectNote]);

        app(TaskStatusHistoryService::class)->transition(
            $task, TaskStatus::IN_PROGRESS, "Dikembalikan oleh Super Admin: {$this->rejectNote}"
        );

        $this->reset(['rejectTaskId', 'rejectNote', 'showRejectModal', 'showDetailModal']);
        session()->flash('message', 'Tugas dikembalikan ke In Progress.');
    }

    public function render()
    {
        $query = Task::where('status', TaskStatus::PENDING_ADMIN)
            ->with(['workspace', 'assignedPm', 'assignedMember', 'creator']);

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        $tasks = $query->latest()->paginate(20);

        $history = null;
        if ($this->historyTaskId) {
            $history = TaskStatusHistory::with('changer')
                ->where('task_id', $this->historyTaskId)
                ->orderBy('created_at')
                ->get();
        }

        return view('livewire.super-admin.task-approval', [
            'tasks' => $tasks,
            'history' => $history,
        ]);
    }
}
