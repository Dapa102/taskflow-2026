<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public User $assigner
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ["database", "mail"];
        if ($notifiable->phone) {
            $channels[] = FonnteChannel::class;
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Tugas Baru: {$this->task->title}")
            ->greeting("Halo {$notifiable->name},")
            ->line("{$this->assigner->name} menugaskan Anda tugas baru:")
            ->line("**{$this->task->title}**")
            ->lineIf($this->task->description, "Deskripsi: {$this->task->description}")
            ->lineIf($this->task->deadline, "Deadline: {$this->task->deadline->format("d M Y")}")
            ->action("Lihat Tugas", url("/tasks/{$this->task->id}"))
            ->line("Segera kerjakan tugas ini.");
    }

    public function toWhatsApp(object $notifiable): string
    {
        $deadline = $this->task->deadline
            ? "\\nDeadline: " . $this->task->deadline->format("d M Y")
            : "";

        return "*Tugas Baru Diberikan* 🎯
Halo {$notifiable->name},
{$this->assigner->name} menugaskan Anda:

*{$this->task->title}*
{$this->task->description}{$deadline}

Link: " . url("/tasks/{$this->task->id}");
    }

    public function toArray(object $notifiable): array
    {
        return [
            "task_id" => $this->task->id,
            "task_title" => $this->task->title,
            "assigned_by" => $this->assigner->name,
            "assigned_by_id" => $this->assigner->id,
            "message" => "{$this->assigner->name} menugaskan Anda: \"{$this->task->title}\"",
        ];
    }
}
