<?php

namespace App\Livewire\Pm;

use App\Enums\TaskStatus;
use Livewire\Component;
use App\Models\Task;
use App\Models\Project;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class PmAllTasks extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $projectFilter = '';
    public $search = '';
    public $showDetailModal = false;
    public $detailTask = null;

    protected $queryString = ['statusFilter', 'projectFilter', 'search'];

    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingProjectFilter() { $this->resetPage(); }
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
            ->where('status', TaskStatus::REVIEW)
            ->findOrFail($taskId);
        
        $task->update(['reviewed_by' => auth()->id()]);

        app(\App\Services\TaskStatusHistoryService::class)->transition(
            $task, TaskStatus::DONE, 'Disetujui Project Manager'
        );

        $this->showDetailModal = false;
        session()->flash('message', 'Tugas berhasil disetujui.');
    }

    public function rejectTask($taskId)
    {
        $this->validate(['reviewNote' => 'required|string|min:3|max:1000']);

        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', TaskStatus::REVIEW)
            ->findOrFail($taskId);

        $task->update([
            'review_note' => $this->reviewNote,
            'reviewed_by' => auth()->id(),
        ]);

        app(\App\Services\TaskStatusHistoryService::class)->transition(
            $task, TaskStatus::IN_PROGRESS, "Dikembalikan untuk perbaikan: {$this->reviewNote}"
        );

        $this->reset(['reviewNote', 'rejectTaskId', 'showDetailModal']);
        session()->flash('message', 'Tugas dikembalikan untuk revisi.');
    }

    public function render()
    {
        $query = Task::where('assigned_pm_id', auth()->id())
            ->with(['workspace', 'assignedMember', 'creator']);

        if ($this->projectFilter) {
            $query->where('project_id', $this->projectFilter);
        }

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->statusFilter === 'done') {
            $query->where('status', TaskStatus::DONE);
        } elseif ($this->statusFilter === 'pending') {
            $query->whereNotIn('status', [TaskStatus::DONE, TaskStatus::CANCELLED]);
        } elseif ($this->statusFilter === 'overdue') {
            $query->whereNotNull('deadline')
                  ->where('deadline', '<', now())
                  ->whereNotIn('status', [TaskStatus::DONE, TaskStatus::CANCELLED]);
        }

        $workspace = auth()->user()->currentWorkspace();
        $projects = $workspace ? $workspace->projects()->orderBy('name')->get() : collect();

        return view('livewire.pm.pm-all-tasks', [
            'tasks' => $query->latest()->paginate(20),
            'projects' => $projects,
        ]);
    }
}
