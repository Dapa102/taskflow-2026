<?php

namespace App\Services;

use App\Enums\TaskStatus;
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
            TaskStatus::TODO . '->' . TaskStatus::TODO => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Baru Ditugaskan', 'message' => "Anda ditugaskan mengerjakan \"{$task->title}\"."],
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Tugas Ditugaskan ke Anggota', 'message' => "Tugas \"{$task->title}\" telah ditugaskan ke anggota."],
            ],
            TaskStatus::TODO . '->' . TaskStatus::IN_PROGRESS => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Mulai Dikerjakan', 'message' => "Tugas \"{$task->title}\" sedang dikerjakan."],
            ],
            TaskStatus::TODO . '->' . TaskStatus::REVIEW => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Baru untuk Anda', 'message' => "Anda ditugaskan mengerjakan \"{$task->title}\"."],
            ],
            TaskStatus::IN_PROGRESS . '->' . TaskStatus::REVIEW => [
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Tugas Selesai Dikerjakan', 'message' => "Tugas \"{$task->title}\" telah selesai dikerjakan anggota. Mohon direview."],
            ],
            TaskStatus::REVIEW . '->' . TaskStatus::IN_PROGRESS => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Perlu Perbaikan', 'message' => "Tugas \"{$task->title}\" perlu diperbaiki. Catatan: {$notes}"],
            ],
            TaskStatus::REVIEW . '->' . TaskStatus::PENDING_ADMIN => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Menunggu Approval', 'message' => "Tugas \"{$task->title}\" telah disetujui PM, menunggu approval Super Admin."],
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Tugas Menunggu Approval SA', 'message' => "Tugas \"{$task->title}\" sedang menunggu approval Super Admin."],
            ],
            TaskStatus::PENDING_ADMIN . '->' . TaskStatus::DONE => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Selesai', 'message' => "Tugas \"{$task->title}\" telah disetujui Super Admin."],
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Tugas Selesai', 'message' => "Tugas \"{$task->title}\" telah mendapatkan persetujuan akhir Super Admin."],
            ],
            TaskStatus::PENDING_ADMIN . '->' . TaskStatus::IN_PROGRESS => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Dikembalikan', 'message' => "Tugas \"{$task->title}\" dikembalikan oleh Super Admin. Catatan: {$notes}"],
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Tugas Dikembalikan SA', 'message' => "Tugas \"{$task->title}\" dikembalikan oleh Super Admin."],
            ],
            'pending_arbitration' . '->' . 'pending_admin' => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Arbitrase Disetujui', 'message' => "Tugas \"{$task->title}\" telah disetujui melalui arbitrase."],
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Arbitrase Disetujui', 'message' => "Tugas \"{$task->title}\" telah disetujui melalui arbitrase, menunggu approval SA."],
            ],
            'pending_arbitration' . '->' . 'revision' => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Arbitrase Ditolak', 'message' => "Tugas \"{$task->title}\" ditolak dalam arbitrase. Catatan: {$notes}"],
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Arbitrase Ditolak', 'message' => "Tugas \"{$task->title}\" ditolak dalam arbitrase."],
            ],
            'pending_pm' . '->' . 'done' => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Eskalasi Disetujui', 'message' => "Tugas \"{$task->title}\" telah disetujui langsung oleh Super Admin."],
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Eskalasi Disetujui', 'message' => "Tugas \"{$task->title}\" disetujui langsung oleh Super Admin (bypass PM)."],
            ],
            'pending_pm' . '->' . 'revision' => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Eskalasi Ditolak', 'message' => "Tugas \"{$task->title}\" ditolak saat eskalasi."],
                ['user_id' => $task->assigned_pm_id, 'subject' => 'Eskalasi Ditolak', 'message' => "Tugas \"{$task->title}\" ditolak saat eskalasi."],
            ],
            TaskStatus::REVIEW . '->' . TaskStatus::DONE => [
                ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Selesai', 'message' => "Tugas \"{$task->title}\" telah disetujui Project Manager."],
            ],
            default => null,
        };

        if ($newStatus === TaskStatus::CANCELLED) {
            $recipients = array_filter([
                ['user_id' => $task->created_by, 'subject' => 'Tugas Dibatalkan', 'message' => "Tugas \"{$task->title}\" telah dibatalkan."],
                $task->assigned_pm_id ? ['user_id' => $task->assigned_pm_id, 'subject' => 'Tugas Dibatalkan', 'message' => "Tugas \"{$task->title}\" telah dibatalkan."] : null,
                $task->assigned_member_id ? ['user_id' => $task->assigned_member_id, 'subject' => 'Tugas Dibatalkan', 'message' => "Tugas \"{$task->title}\" telah dibatalkan."] : null,
            ]);
        }

        if ($recipients) {
            foreach ($recipients as $r) {
                if ($r['user_id']) {
                    $this->sendInboxNotification($task, $r['user_id'], $r['subject'], $r['message']);
                }
            }
        }

        if ($newStatus === TaskStatus::DONE) {
            $superAdmins = User::where('role', 'super_admin')->pluck('id');
            foreach ($superAdmins as $saId) {
                $this->sendInboxNotification($task, $saId, 'Tugas Selesai', "Tugas \"{$task->title}\" telah selesai disetujui Project Manager.");
            }
        }
    }
}
