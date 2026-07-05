<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Models\Team;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.super-admin')]
class TaskOversight extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $search = '';
    public $selectedTaskId = null;
    public $detailTask = null;

    public function mount($taskId = null)
    {
        if ($taskId) {
            $this->viewDetail($taskId);
        }
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewDetail($taskId)
    {
        $this->selectedTaskId = $taskId;
        $this->detailTask = Task::with(['workspace', 'creator', 'assignee', 'attachments'])->find($taskId);
    }

    public function closeDetail()
    {
        $this->selectedTaskId = null;
        $this->detailTask = null;
    }

    public function assignToPm($taskId, $pmId)
    {
        $task = Task::findOrFail($taskId);
        $pm = User::findOrFail($pmId);

        $task->update([
            'assigned_pm_id' => $pmId,
            'status' => 'assigned_pm',
        ]);

        $workspace = $task->workspace;
        if ($workspace && $workspace->pm_id !== $pmId) {
            $workspace->update(['pm_id' => $pmId]);
        }

        app(\App\Services\TaskStatusHistoryService::class)->record(
            $task, 'draft', 'assigned_pm', "Ditugaskan ke PM oleh Super Admin"
        );

        session()->flash('message', "Task assigned to PM {$pm->name}.");
        $this->closeDetail();
    }

    public function render()
    {
        $query = Task::with(['workspace', 'assignee', 'creator', 'attachments'])
            ->whereNotIn('status', ['done', 'cancelled']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('workspace', function ($wq) {
                      $wq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter === 'pending') {
            $query->whereNull('assigned_pm_id');
        } elseif ($this->statusFilter === 'given') {
            $query->whereNotNull('assigned_pm_id');
        } elseif ($this->statusFilter === 'overdue') {
            $query->whereNotNull('deadline')
                  ->where('deadline', '<', now())
                  ->where('status', '!=', 'done');
        } elseif ($this->statusFilter === 'done') {
            $query->where('status', 'done');
        }

        $pms = User::where('role', 'pm')->get();

        return view('livewire.super-admin.task-oversight', [
            'tasks' => $query->latest()->paginate(15),
            'pms' => $pms,
        ]);
    }
}
