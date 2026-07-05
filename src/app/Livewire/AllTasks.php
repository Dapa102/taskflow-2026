<?php

namespace App\Livewire;

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
            $query->where('status', 'done');
        } elseif ($this->statusFilter === 'pending') {
            $query->where('status', '!=', 'done');
        } elseif ($this->statusFilter === 'overdue') {
            $query->whereNotNull('deadline')
                  ->where('deadline', '<', now())
                  ->where('status', '!=', 'done');
        }

        return view('livewire.all-tasks', [
            'tasks' => $query->latest()->paginate(20),
        ]);
    }
}
