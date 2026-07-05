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
            ->where('status', 'pending_pm')
            ->findOrFail($taskId);

        app(TaskStatusHistoryService::class)->transition(
            $task, 'pending_admin', $this->reviewNote ?: 'Disetujui PM, menunggu approval admin'
        );

        $this->reviewNote = '';
    }

    public function reject($taskId)
    {
        $this->validate(['reviewNote' => 'required|min:3|max:1000']);

        $task = Task::where('assigned_pm_id', auth()->id())
            ->where('status', 'pending_pm')
            ->findOrFail($taskId);

        $newCounter = $task->revision_counter + 1;
        $task->update([
            'review_note' => $this->reviewNote,
            'revision_counter' => $newCounter,
        ]);

        if ($task->isRevisiLocked() || $newCounter >= $task->max_revision_limit) {
            app(TaskStatusHistoryService::class)->transition(
                $task, 'pending_arbitration', "Batas revisi tercapai ({$newCounter}/{$task->max_revision_limit}): {$this->reviewNote}"
            );
        } else {
            app(TaskStatusHistoryService::class)->transition(
                $task, 'revision', "Revisi ({$newCounter}/{$task->max_revision_limit}): {$this->reviewNote}"
            );
        }

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
