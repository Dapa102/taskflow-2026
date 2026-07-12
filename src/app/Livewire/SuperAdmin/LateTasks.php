<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Task;
use App\Models\Workspace;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

#[Layout('layouts.super-admin')]
class LateTasks extends Component
{
    use WithPagination;

    public $workspaceFilter = '';
    public $startDate = '';
    public $endDate = '';
    public $search = '';

    public function exportPdf()
    {
        $tasks = $this->getLateTasksQuery()->get();
        $pdf = Pdf::loadView('pdf.late-tasks', ['tasks' => $tasks]);
        return response()->streamDownload(fn() => print($pdf->output()), 'late-tasks.pdf');
    }

    public function render()
    {
        return view('livewire.super-admin.late-tasks', [
            'tasks' => $this->getLateTasksQuery()->paginate(15),
            'workspaces' => Workspace::orderBy('name')->get(),
        ]);
    }

    private function getLateTasksQuery()
    {
        $query = Task::with(['workspace', 'assignedPm', 'assignedMember'])
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->where('status', '!=', 'done');

        if ($this->workspaceFilter) {
            $query->where('workspace_id', $this->workspaceFilter);
        }
        if ($this->startDate) {
            $query->whereDate('deadline', '>=', Carbon::parse($this->startDate));
        }
        if ($this->endDate) {
            $query->whereDate('deadline', '<=', Carbon::parse($this->endDate));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhereHas('assignedPm', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('assignedMember', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
            });
        }

        return $query->orderBy('deadline');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
