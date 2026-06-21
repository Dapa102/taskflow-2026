<?php

namespace App\Livewire\Member;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
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

        $pm = User::where('role', 'pm')
            ->whereHas('workspace', function ($q) {
                $q->whereHas('members', fn ($q2) => $q2->where('user_id', auth()->id()));
            })
            ->first();

        return view('livewire.member.member-dashboard', [
            'tasks' => $tasks,
            'pm' => $pm,
        ]);
    }
}
