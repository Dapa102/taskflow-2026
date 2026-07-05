<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Task;
use App\Services\TaskStatusHistoryService;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class ReviewTasks extends Component
{
    public $reviewNote = '';
    public $taskId;

    public function approve($taskId)
    {
        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', 'pending_review')
            ->findOrFail($taskId);

        $task->update([
            'status' => 'done',
            'review_note' => $this->reviewNote ?: null,
        ]);

        app(TaskStatusHistoryService::class)->record(
            $task, 'done', 'done', 'Tugas disetujui oleh PM'
        );

        session()->flash('message', 'Tugas "' . $task->title . '"  disetujui.');
        $this->reviewNote = '';
    }

    public function reject($taskId)
    {
        $this->validate(['reviewNote' => 'required|min:3|max:1000']);

        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', 'pending_review')
            ->findOrFail($taskId);

        $task->update([
            'status' => 'assigned_member',
            'review_note' => $this->reviewNote,
        ]);

        app(TaskStatusHistoryService::class)->record(
            $task, 'assigned_member', 'revision', 'Tugas direvisi: ' . $this->reviewNote
        );

        session()->flash('message', 'Tugas "' . $task->title . '" dikembalikan ke anggota.');
        $this->reviewNote = '';
    }

    public function render()
    {
        $tasks = Task::where('assigned_pm_id', auth()->id())
            ->with(['project', 'assignedMember', 'creator'])
            ->latest()
            ->get();

        return view('livewire.pm.review-tasks', compact('tasks'));
    }
}
