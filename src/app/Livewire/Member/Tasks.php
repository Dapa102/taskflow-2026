<?php

namespace App\Livewire\Member;

use Livewire\Component;
use App\Models\Task;
use App\Services\TaskStatusHistoryService;
use Livewire\Attributes\Layout;

#[Layout('layouts.member')]
class Tasks extends Component
{
    public $upload = [];
    public $uploadingTaskId;
    public $submitTaskId = null;

    public function submitTask($taskId)
    {
        $task = Task::where('assigned_member_id', auth()->id())
            ->whereIn('status', ['assigned_member', 'revision'])
            ->findOrFail($taskId);

        app(TaskStatusHistoryService::class)->transition(
            $task, 'pending_pm', 'Tugas diserahkan oleh anggota'
        );

        $task->update(['submitted_at' => now()]);

        session()->flash('message', 'Tugas berhasil dikirim untuk direview PM.');
    }

    public function render()
    {
        $tasks = Task::where('assigned_member_id', auth()->id())
            ->with('project')
            ->latest()
            ->get();

        return view('livewire.member.tasks', compact('tasks'));
    }
}
