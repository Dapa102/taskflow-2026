<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCommentNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Comment $comment
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
        $task = $this->comment->task;

        return (new MailMessage)
            ->subject("Komentar Baru: {$task->title}")
            ->greeting("Halo {$notifiable->name},")
            ->line("{$this->comment->user->name} berkomentar pada tugas \"{$task->title}\":")
            ->line("> {$this->comment->content}")
            ->action("Lihat Tugas", url("/tasks/{$task->id}"))
            ->line("Balas komentar ini di aplikasi.");
    }

    public function toWhatsApp(object $notifiable): string
    {
        $task = $this->comment->task;

        return "*Komentar Baru* 💬
Halo {$notifiable->name},
{$this->comment->user->name} berkomentar di tugas *{$task->title}*:

> {$this->comment->content}

Link: " . url("/tasks/{$task->id}");
    }

    public function toArray(object $notifiable): array
    {
        return [
            "task_id" => $this->comment->task_id,
            "task_title" => $this->comment->task->title,
            "comment_by" => $this->comment->user->name,
            "comment_id" => $this->comment->id,
            "message" => "{$this->comment->user->name} berkomentar di \"{$this->comment->task->title}\"",
        ];
    }
}
