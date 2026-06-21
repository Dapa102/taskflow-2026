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

    public $showForm = false;
    public $title = '';
    public $description = '';
    public $priority = 'medium';
    public $deadline = '';
    public $selectedPm = '';
    public $workspaceId = '';
    public $statusFilter = 'all';
    public $search = '';

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'nullable|max:1000',
        'priority' => 'required|in:low,medium,high',
        'deadline' => 'nullable|date|after_or_equal:today',
        'selectedPm' => 'required|exists:users,id',
        'workspaceId' => 'required|exists:workspaces,id',
    ];

    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public function createTask()
    {
        $this->validate();

        Task::create([
            'workspace_id' => $this->workspaceId,
            'created_by' => auth()->id(),
            'assigned_to' => $this->selectedPm,
            'title' => $this->title,
            'description' => $this->description,
            'status' => 'todo',
            'priority' => $this->priority,
            'deadline' => $this->deadline ?: null,
        ]);

        session()->flash('message', 'Tugas berhasil dibuat.');
        $this->reset(['title', 'description', 'priority', 'deadline', 'selectedPm', 'workspaceId', 'showForm']);
    }

    public function deleteTask($id)
    {
        Task::findOrFail($id)->delete();
        session()->flash('message', 'Tugas dihapus.');
    }

    public function render()
    {
        $query = Task::with(['workspace', 'assignee', 'creator']);

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

        $pms = User::where('role', 'pm')->get();
        $workspaces = Workspace::with('pm')->latest()->get();
        $pmTeams = collect();
        if ($this->selectedPm) {
            $pmTeams = Team::where('owner_id', $this->selectedPm)
                ->with('members.user')
                ->get();
        }

        return view('livewire.admin.task-list', [
            'tasks' => $query->latest()->paginate(20),
            'pms' => $pms,
            'workspaces' => $workspaces,
            'pmTeams' => $pmTeams,
        ]);
    }
}
