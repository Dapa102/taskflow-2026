<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Team;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class TaskList extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $search = '';
    public $selectedTask = null;
    public $showDetail = false;

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'nullable|max:1000',
        'priority' => 'required|in:low,medium,high',
        'deadline' => 'nullable|date|after_or_equal:today',
        'selectedPm' => 'required|exists:users,id',
        'workspaceId' => 'required|exists:workspaces,id',
    ];

    public $title = '';
    public $description = '';
    public $priority = 'medium';
    public $deadline = '';
    public $selectedPm = '';
    public $workspaceId = '';

    public function viewTask($id)
    {
        $this->selectedTask = Task::with(['workspace', 'assignee', 'creator', 'attachments'])->findOrFail($id);
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedTask = null;
    }

    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public function deleteTask($id)
    {
        Task::findOrFail($id)->delete();
        session()->flash('message', 'Tugas dihapus.');
    }

    public function finalApproveTask($id)
    {
        $task = Task::with('attachments')->findOrFail($id);
        if ($task->status !== 'pending_admin') {
            session()->flash('error', 'Task is not pending admin review.');
            return;
        }
        if ($task->attachments->isEmpty()) {
            session()->flash('error', 'Tugas wajib melampirkan file sebelum diselesaikan.');
            return;
        }
        $task->update(['status' => 'done']);
        session()->flash('message', 'Tugas selesai dikonfirmasi.');
    }

    public function render()
    {
        $query = Task::with(['workspace', 'assignee', 'creator', 'attachments'])
            ->whereNotNull('assigned_to');

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

        return view('livewire.admin.task-list', [
            'tasks' => $query->latest()->paginate(20),
        ]);
    }
}
