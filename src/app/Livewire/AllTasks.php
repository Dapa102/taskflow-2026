<?php

namespace App\Livewire;

use App\Enums\TaskStatus;
use Livewire\Component;
use App\Models\Task;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AllTasks extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $search = '';

    public $detailModal = false;
    public $detailTask = null;

    protected $queryString = ['statusFilter', 'search'];

    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public function showDetail($id)
    {
        $this->detailTask = Task::with(['workspace', 'assignee', 'creator', 'attachments', 'comments.user'])
            ->findOrFail($id);
        $this->detailModal = true;
    }

    public function render()
    {
        $query = Task::with(['workspace', 'assignee', 'creator']);

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

        return view('livewire.all-tasks', [
            'tasks' => $query->latest()->paginate(20),
        ]);
    }
}
