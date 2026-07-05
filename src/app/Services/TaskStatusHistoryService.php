<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
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

        $fresh = $task->fresh();

        $this->notifyTransition($fresh, $oldStatus, $newStatus, $notes);

        return $fresh;
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

    public function notifyTransition(Task $task, string $oldStatus, string $newStatus, ?string $notes = null): void
    {
        $key = "{$oldStatus}->{$newStatus}";

        $recipients = match ($key) {
            'draft->assigned_pm' => [
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Tugas Baru Dikirim ke Anda', 'message' => "Tugas \"{$task->title}\" telah dikirim ke Anda untuk ditugaskan ke anggota."],
            ],
            'assigned_pm->assigned_member' => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Baru untuk Anda', 'message' => "Anda ditugaskan mengerjakan \"{$task->title}\"."],
            ],
            'assigned_member->pending_pm' => [
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Tugas Selesai Dikerjakan', 'message' => "Tugas \"{$task->title}\" telah selesai dikerjakan anggota. Mohon direview."],
            ],
            'pending_pm->revision' => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Perlu Revisi', 'message' => "Tugas \"{$task->title}\" perlu direvisi. Catatan: {$notes}"],
            ],
            'revision->pending_pm' => [
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Tugas Direvisi', 'message' => "Tugas \"{$task->title}\" telah direvisi anggota. Mohon direview kembali."],
            ],
            'pending_admin->done' => [
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Tugas Selesai', 'message' => "Tugas \"{$task->title}\" telah selesai."],
            ],
            default => null,
        };

        if (in_array($newStatus, ['pending_arbitration', 'cancelled'])) {
            $recipients = match ($newStatus) {
                'pending_arbitration' => [
                    ['user_id' => $task->created_by, 'subject' => 'Arbitrase: Batas Revisi Tercapai', 'message' => "Tugas \"{$task->title}\" masuk arbitrase karena batas revisi tercapai ({$task->revision_counter}/{$task->max_revision_limit})."],
                ],
                'cancelled' => array_filter([
                    ['user_id' => $task->created_by, 'subject' => 'Tugas Dibatalkan', 'message' => "Tugas \"{$task->title}\" telah dibatalkan."],
                    $task->assigned_pm_id ? ['user_id' => $task->assigned_pm_id, 'subject' => 'Tugas Dibatalkan', 'message' => "Tugas \"{$task->title}\" telah dibatalkan."] : null,
                    $task->assigned_member_id ? ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Dibatalkan', 'message' => "Tugas \"{$task->title}\" telah dibatalkan."] : null,
                ]),
                default => [],
            };
        }

        if ($recipients) {
            foreach ($recipients as $r) {
                if ($r['user_id']) {
                    $this->sendInboxNotification($task, $r['user_id'], $r['subject'], $r['message']);
                }
            }
        }

        if (in_array($newStatus, ['pending_admin', 'done', 'pending_arbitration'])) {
            $superAdmins = User::where('role', 'super_admin')->pluck('id');
            $subject = match ($newStatus) {
                'pending_admin' => 'Tugas Menunggu Approval',
                'done' => 'Tugas Selesai',
                'pending_arbitration' => 'Arbitrase: Batas Revisi Tercapai',
            };
            $message = match ($newStatus) {
                'pending_admin' => "Tugas \"{$task->title}\" telah disetujui PM dan menunggu approval Anda.",
                'done' => "Tugas \"{$task->title}\" telah selesai disetujui.",
                'pending_arbitration' => "Tugas \"{$task->title}\" masuk arbitrase karena batas revisi tercapai ({$task->revision_counter}/{$task->max_revision_limit}).",
            };
            foreach ($superAdmins as $saId) {
                $this->sendInboxNotification($task, $saId, $subject, $message);
            }
        }
    }
}
