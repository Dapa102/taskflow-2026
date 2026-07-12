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

    public $deleteTaskId;
    public $showEditModal = false;
    public $editTaskId;
    public $editProjectId;
    public $editTitle;
    public $editDescription;
    public $editPriority;
    public $editDeadline;
    public $editAssigneeId;

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
            $task, TaskStatus::PENDING_ADMIN, 'Disetujui Project Manager, menunggu approval Super Admin'
        );

        $this->showDetailModal = false;
        session()->flash('message', 'Tugas disetujui, menunggu approval Super Admin.');
    }

    public function rejectTask($taskId)
    {
        $this->validate(['reviewNote' => 'required|string|min:3|max:1000']);

        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', TaskStatus::REVIEW)
            ->findOrFail($taskId);

        $newCounter = $task->revision_counter + 1;
        $task->update([
            'review_note' => $this->reviewNote,
            'reviewed_by' => auth()->id(),
            'revision_counter' => $newCounter,
        ]);

        if ($newCounter >= $task->max_revision_limit) {
            app(\App\Services\TaskStatusHistoryService::class)->transition(
                $task, 'pending_arbitration', "Batas revisi tercapai, masuk arbitrase: {$this->reviewNote} ({$newCounter}/{$task->max_revision_limit})"
            );
            session()->flash('message', 'Batas revisi tercapai. Task masuk arbitrase.');
        } else {
            app(\App\Services\TaskStatusHistoryService::class)->transition(
                $task, TaskStatus::IN_PROGRESS, "Dikembalikan untuk perbaikan: {$this->reviewNote} ({$newCounter}/{$task->max_revision_limit})"
            );
            session()->flash('message', 'Tugas dikembalikan untuk revisi.');
        }

        $this->reset(['reviewNote', 'rejectTaskId', 'showDetailModal']);
    }

    public function confirmDelete($taskId)
    {
        $this->deleteTaskId = $taskId;
    }

    public function deleteTask($taskId)
    {
        $task = Task::where('assigned_pm_id', auth()->id())->findOrFail($taskId);
        $task->delete();
        $this->deleteTaskId = null;
        session()->flash('message', 'Tugas berhasil dihapus.');
    }

    public function editTask($taskId)
    {
        $task = Task::where('assigned_pm_id', auth()->id())->findOrFail($taskId);
        $this->editTaskId = $task->id;
        $this->editProjectId = (string) $task->project_id;
        $this->editTitle = $task->title;
        $this->editDescription = $task->description;
        $this->editPriority = $task->priority;
        $this->editDeadline = $task->deadline?->format('Y-m-d');
        $this->editAssigneeId = (string) $task->assigned_member_id;
        $this->showEditModal = true;
    }

    public function updateTask()
    {
        $this->validate([
            'editProjectId' => 'required|exists:projects,id',
            'editTitle' => 'required|min:3|max:255',
            'editDescription' => 'nullable|max:2000',
            'editPriority' => 'required|in:low,medium,high',
            'editDeadline' => 'nullable|date',
            'editAssigneeId' => 'required|exists:users,id',
        ]);

        $task = Task::where('assigned_pm_id', auth()->id())->findOrFail($this->editTaskId);
        $oldStatus = $task->status;

        $task->update([
            'project_id' => $this->editProjectId,
            'title' => $this->editTitle,
            'description' => $this->editDescription,
            'priority' => $this->editPriority,
            'deadline' => $this->editDeadline ?: null,
            'assigned_member_id' => $this->editAssigneeId,
            'assigned_to' => $this->editAssigneeId,
        ]);

        if ($oldStatus === $task->status) {
            app(\App\Services\TaskStatusHistoryService::class)->record(
                $task, $oldStatus, $task->status, 'Tugas diubah oleh PM'
            );
        }

        $this->reset(['showEditModal', 'editTaskId', 'editProjectId', 'editTitle', 'editDescription', 'editPriority', 'editDeadline', 'editAssigneeId']);
        session()->flash('message', 'Tugas berhasil diperbarui.');
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
