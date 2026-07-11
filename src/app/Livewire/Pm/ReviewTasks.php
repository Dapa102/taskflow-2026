<?php

namespace App\Livewire\Pm;

use App\Enums\TaskStatus;
use Livewire\Component;
use App\Models\Task;
use App\Services\TaskStatusHistoryService;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class ReviewTasks extends Component
{
    public $reviewNote = '';
    public $taskId;
    public $showDetailModal = false;
    public $detailTask = null;

    public function showDetail($taskId)
    {
        $this->detailTask = Task::with(['project', 'assignedMember', 'creator', 'attachments'])
            ->where('assigned_pm_id', auth()->id())
            ->findOrFail($taskId);
        $this->showDetailModal = true;
    }

    public function approve($taskId)
    {
        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', TaskStatus::REVIEW)
            ->findOrFail($taskId);

        $task->update(['reviewed_by' => auth()->id()]);

        app(TaskStatusHistoryService::class)->transition(
            $task, TaskStatus::DONE, $this->reviewNote ?: 'Disetujui Project Manager'
        );

        $this->reviewNote = '';
        $this->showDetailModal = false;
    }

    public function reject($taskId)
    {
        $this->validate(['reviewNote' => 'required|min:3|max:1000']);

        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', TaskStatus::REVIEW)
            ->findOrFail($taskId);

        $task->update([
            'review_note' => $this->reviewNote,
            'reviewed_by' => auth()->id(),
        ]);

        app(TaskStatusHistoryService::class)->transition(
            $task, TaskStatus::IN_PROGRESS, "Dikembalikan untuk perbaikan: {$this->reviewNote}"
        );

        $this->reviewNote = '';
        $this->showDetailModal = false;
    }

    public function render()
    {
        $tasks = Task::where('assigned_pm_id', auth()->id())
            ->where('status', TaskStatus::REVIEW)
            ->with(['project', 'assignedMember', 'creator'])
            ->latest()
            ->get();

        return view('livewire.pm.review-tasks', compact('tasks'));
    }
}
