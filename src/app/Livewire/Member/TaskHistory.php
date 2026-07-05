<?php

namespace App\Livewire\Member;

use Livewire\Component;
use App\Models\Task;
use Livewire\Attributes\Layout;

#[Layout('layouts.member')]
class TaskHistory extends Component
{
    public $detailModal = false;
    public $detailTask = null;

    public function showDetail($taskId)
    {
        $this->detailTask = Task::with(['workspace', 'assignedPm', 'creator', 'attachments'])
            ->where('assigned_member_id', auth()->id())
            ->findOrFail($taskId);
        $this->detailModal = true;
    }

    public function render()
    {
        $tasks = Task::where('assigned_member_id', auth()->id())
            ->whereIn('status', ['done', 'cancelled'])
            ->with(['project', 'workspace', 'assignedPm', 'creator'])
            ->latest()
            ->get();

        return view('livewire.member.task-history', compact('tasks'));
    }
}
