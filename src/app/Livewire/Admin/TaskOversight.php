<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Models\Team;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.admin')]
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
            'assigned_to' => $pmId,
            'status' => 'todo',
        ]);

        $workspace = $task->workspace;
        if ($workspace && $workspace->pm_id !== $pmId) {
            $workspace->update(['pm_id' => $pmId]);
        }

        session()->flash('message', "Task assigned to PM {$pm->name}.");
        $this->closeDetail();
    }

    public function render()
    {
        $query = Task::with(['workspace', 'assignee', 'creator', 'attachments'])
            ->whereHas('creator', function ($q) {
                $q->where('role', 'super_admin');
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('workspace', function ($wq) {
                      $wq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter === 'pending') {
            $query->whereNull('assigned_to');
        } elseif ($this->statusFilter === 'given') {
            $query->whereNotNull('assigned_to');
        } elseif ($this->statusFilter === 'overdue') {
            $query->whereNotNull('deadline')
                  ->where('deadline', '<', now())
                  ->where('status', '!=', 'done');
        } elseif ($this->statusFilter === 'done') {
            $query->where('status', 'done');
        }

        $pms = User::where('role', 'pm')->get();

        return view('livewire.admin.task-oversight', [
            'tasks' => $query->latest()->paginate(15),
            'pms' => $pms,
        ]);
    }
}
