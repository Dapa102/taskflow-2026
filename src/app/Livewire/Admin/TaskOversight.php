<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Task;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TaskOversight extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $search = '';

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Task::with(['workspace', 'assignee', 'creator']);

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('workspace', function($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        }

        if ($this->statusFilter === 'overdue') {
            $query->whereNotNull('deadline')
                  ->where('deadline', '<', now())
                  ->where('status', '!=', 'done');
        } elseif ($this->statusFilter === 'done') {
            $query->where('status', 'done');
        } elseif ($this->statusFilter === 'pending') {
            $query->where('status', '!=', 'done');
        }

        return view('livewire.admin.task-oversight', [
            'tasks' => $query->latest()->paginate(15)
        ]);
    }
}
