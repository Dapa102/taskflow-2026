<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Task;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class PmAllTasks extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $search = '';
    public $showDetailModal = false;
    public $detailTask = null;

    protected $queryString = ['statusFilter', 'search'];

    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public $reviewNote;
    public $rejectTaskId;

    public function showDetail($taskId)
    {
        $this->detailTask = Task::with(['workspace', 'assignedMember', 'creator', 'attachments'])
            ->where('assigned_pm_id', auth()->id())
            ->findOrFail($taskId);
        $this->showDetailModal = true;
    }

    public function approveTask($taskId)
    {
        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', 'pending_pm')
            ->findOrFail($taskId);
        
        $task->update(['reviewed_by' => auth()->id()]);

        app(\App\Services\TaskStatusHistoryService::class)->transition(
            $task, 'pending_admin', 'Disetujui PM, menunggu approval admin'
        );

        $this->showDetailModal = false;
        session()->flash('message', 'Tugas berhasil disetujui.');
    }

    public function rejectTask($taskId)
    {
        $this->validate(['reviewNote' => 'required|string|min:3|max:1000']);

        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', 'pending_pm')
            ->findOrFail($taskId);

        $newCounter = $task->revision_counter + 1;
        $task->update([
            'review_note' => $this->reviewNote,
            'reviewed_by' => auth()->id(),
            'revision_counter' => $newCounter,
        ]);

        if ($task->isRevisiLocked() || $newCounter >= $task->max_revision_limit) {
            app(\App\Services\TaskStatusHistoryService::class)->transition(
                $task, 'pending_arbitration', "Batas revisi tercapai ({$newCounter}/{$task->max_revision_limit}): {$this->reviewNote}"
            );
        } else {
            app(\App\Services\TaskStatusHistoryService::class)->transition(
                $task, 'revision', "Revisi ({$newCounter}/{$task->max_revision_limit}): {$this->reviewNote}"
            );
        }

        $this->reset(['reviewNote', 'rejectTaskId', 'showDetailModal']);
        session()->flash('message', 'Tugas dikembalikan untuk revisi.');
    }

    public function render()
    {
        $query = Task::where('assigned_pm_id', auth()->id())
            ->with(['workspace', 'assignedMember', 'creator']);

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->statusFilter === 'done') {
            $query->where('status', 'done');
        } elseif ($this->statusFilter === 'pending') {
            $query->where('status', '!=', 'done');
        } elseif ($this->statusFilter === 'overdue') {
            $query->whereNotNull('deadline')
                  ->where('deadline', '<', now())
                  ->where('status', '!=', 'done');
        }

        return view('livewire.pm.pm-all-tasks', [
            'tasks' => $query->latest()->paginate(20),
        ]);
    }
}
