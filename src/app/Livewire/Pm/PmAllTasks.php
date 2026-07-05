<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Task;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class PmAllTasks extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $search = '';

    protected $queryString = ['statusFilter', 'search'];

    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $query = Task::where('assigned_pm_id', auth()->id())
            ->with(['workspace', 'assignedMember', 'creator']);

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

        return view('livewire.pm.pm-all-tasks', [
            'tasks' => $query->latest()->paginate(20),
        ]);
    }
}
