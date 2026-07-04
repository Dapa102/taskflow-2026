<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use Livewire\Attributes\Layout;

#[Layout('layouts.super-admin')]
class ArbitrationRecap extends Component
{
    public $selectedTaskId = null;

    public function render()
    {
        $tasks = Task::whereIn('status', ['done', 'pending_admin', 'revision', 'cancelled', 'pending_arbitration'])
            ->whereHas('statusHistories', fn($q) => $q->where('to_status', 'pending_arbitration'))
            ->with(['assignedPm', 'assignedMember', 'creator'])
            ->latest()
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'final_status' => $t->status,
                'pm' => $t->assignedPm?->name,
                'member' => $t->assignedMember?->name,
                'creator' => $t->creator?->name,
                'revision_counter' => $t->revision_counter,
                'max_revision_limit' => $t->max_revision_limit,
                'arbitration_time' => $t->statusHistories->firstWhere('to_status', 'pending_arbitration')?->created_at,
                'arbitration_decision' => $t->statusHistories->firstWhere('from_status', 'pending_arbitration')?->notes,
                'arbitration_outcome' => $t->statusHistories->firstWhere('from_status', 'pending_arbitration')?->to_status,
            ]);

        $detail = null;
        if ($this->selectedTaskId) {
            $task = Task::with(['assignedPm', 'assignedMember', 'creator'])->find($this->selectedTaskId);
            $histories = TaskStatusHistory::with('changer')
                ->where('task_id', $this->selectedTaskId)
                ->orderBy('created_at')
                ->get();
            $detail = ['task' => $task, 'histories' => $histories];
        }

        return view('livewire.admin.arbitration-recap', [
            'tasks' => $tasks,
            'detail' => $detail,
        ]);
    }
}
