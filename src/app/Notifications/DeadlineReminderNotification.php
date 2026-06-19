<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeadlineReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $reminderType = 'tomorrow'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->reminderType === 'overdue'
            ? "⚠️ Tugas Overdue: {$this->task->title}"
            : "🔔 Deadline Besok: {$this->task->title}";

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Halo {$notifiable->name},")
            ->line($this->reminderType === 'overdue'
                ? "Tugas \"{$this->task->title}\" sudah melewati deadline ({$this->task->deadline->format('d M Y')})."
                : "Tugas \"{$this->task->title}\" memiliki deadline besok ({$this->task->deadline->format('d M Y')}).")
            ->action('Lihat Tugas', url("/admin/tasks/{$this->task->id}/edit"))
            ->line('Segera selesaikan tugas ini agar tidak terlewat!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'deadline' => $this->task->deadline->toDateString(),
            'reminder_type' => $this->reminderType,
            'message' => $this->reminderType === 'overdue'
                ? "Tugas \"{$this->task->title}\" sudah overdue!"
                : "Tugas \"{$this->task->title}\" deadline besok.",
        ];
    }
}
