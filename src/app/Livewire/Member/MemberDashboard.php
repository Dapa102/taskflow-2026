<?php

namespace App\Livewire\Member;

use Livewire\Component;
use App\Models\Task;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class MemberDashboard extends Component
{
    public function updateStatus($taskId, $newStatus)
    {
        $task = Task::find($taskId);
        
        if (!$task) return;

        // Policy check
        if (!auth()->user()->can('changeStatus', $task)) {
            session()->flash('error', 'Unauthorized to update this task.');
            return;
        }

        if (in_array($newStatus, ['todo', 'on_progress', 'done'])) {
            $task->update(['status' => $newStatus]);
            session()->flash('message', 'Task status updated.');
        }
    }

    public function render()
    {
        $tasks = Task::where('assigned_to', auth()->id())
            ->with('workspace')
            ->latest()
            ->get();

        return view('livewire.member.member-dashboard', [
            'tasks' => $tasks
        ]);
    }
}
