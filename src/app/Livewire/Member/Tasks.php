<?php

namespace App\Livewire\Member;

use Livewire\Component;
use App\Models\Task;
use Livewire\Attributes\Layout;

#[Layout('layouts.member')]
class Tasks extends Component
{
    public $taskId;
    public $statusNote = '';

    public function updateStatus($taskId, $status)
    {
        $task = Task::where('id', $taskId)->where('assigned_member_id', auth()->id())->firstOrFail();

        $allowed = [
            'in_progress',
            'pending_review',
        ];

        if (!in_array($status, $allowed)) {
            session()->flash('error', 'Status tidak valid.');
            return;
        }

        $task->status = $status;
        $task->save();

        app(\App\Services\TaskStatusHistoryService::class)->record(
            $task, $status, $status, 'Status diperbarui oleh anggota'
        );

        session()->flash('message', 'Status tugas berhasil diperbarui.');
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
