<?php

namespace App\Livewire\Atasan;

use Livewire\Component;
use App\Models\Task;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.atasan')]
class AtasanTaskList extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $search = '';

    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $query = Task::with(['workspace', 'assignee', 'attachments'])
            ->where('created_by', auth()->id());

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->statusFilter === 'given') {
            $query->whereNotNull('assigned_to');
        } elseif ($this->statusFilter === 'pending') {
            $query->whereNull('assigned_to');
        } elseif ($this->statusFilter === 'done') {
            $query->where('status', 'done');
        }

        return view('livewire.atasan.atasan-task-list', [
            'tasks' => $query->latest()->paginate(20),
        ]);
    }
}
