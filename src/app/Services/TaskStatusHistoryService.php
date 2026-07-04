<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskStatusHistory;
use App\Models\InboxNotification;
use Illuminate\Support\Facades\DB;

class TaskStatusHistoryService
{
    public function record(Task $task, string $fromStatus, string $toStatus, ?string $notes = null): TaskStatusHistory
    {
        return TaskStatusHistory::create([
            'task_id' => $task->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by' => auth()->id(),
            'notes' => $notes,
            'created_at' => now(),
        ]);
    }

    public function transition(Task $task, string $newStatus, ?string $notes = null): Task
    {
        $oldStatus = $task->status;

        DB::transaction(function () use ($task, $oldStatus, $newStatus, $notes) {
            $task->update(['status' => $newStatus]);

            $this->record($task, $oldStatus, $newStatus, $notes);
        });

        return $task->fresh();
    }

    public function sendInboxNotification(Task $task, int $userId, string $subject, string $message): InboxNotification
    {
        return InboxNotification::create([
            'user_id' => $userId,
            'task_id' => $task->id,
            'channel' => 'inbox',
            'subject' => $subject,
            'message' => $message,
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
