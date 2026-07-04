<?php

namespace App\Livewire\Atasan;

use Livewire\Component;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use App\Services\TaskStatusHistoryService;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.atasan')]
class AtasanTaskList extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $search = '';
    public $showHistoryModal = false;
    public $historyTaskId = null;
    public $cancelNote = '';
    public $showCancelModal = false;
    public $cancelTaskId = null;

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

        $task = Task::where('created_by', auth()->id())->findOrFail($this->cancelTaskId);

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

    public function render()
    {
        $query = Task::with(['workspace', 'assignee', 'assignedPm', 'assignedMember', 'attachments'])
            ->where('created_by', auth()->id());

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

        return view('livewire.atasan.atasan-task-list', [
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
