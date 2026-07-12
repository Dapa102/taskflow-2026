<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $subject,
        public string $message,
        public ?string $actionText = null,
        public ?string $actionUrl = null,
    ) {}

    public function via(object $notifiable): array
    {
        return $notifiable->email ? ['mail'] : [];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->subject)
            ->greeting("Halo {$notifiable->name},")
            ->line($this->message)
            ->line("Tugas: {$this->task->title}")
            ->line("Status: " . \App\Enums\TaskStatus::label($this->task->status));

        if ($this->actionText && $this->actionUrl) {
            $mail->action($this->actionText, $this->actionUrl);
        }

        return $mail->salutation('Tim TaskFlow');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'subject' => $this->subject,
            'message' => $this->message,
        ];
    }
}
