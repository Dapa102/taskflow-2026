<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\DeadlineReminderNotification;
use Illuminate\Console\Command;

class SendDeadlineReminders extends Command
{
    protected $signature = 'reminders:deadline';

    protected $description = 'Send deadline reminders for tasks due tomorrow and overdue tasks';

    public function handle(): int
    {
        $tomorrow = now()->addDay()->toDateString();

        $tasksDueTomorrow = Task::where('status', '!=', 'done')
            ->where('deadline', $tomorrow)
            ->with('user')
            ->get();

        foreach ($tasksDueTomorrow as $task) {
            $task->user->notify(new DeadlineReminderNotification($task, 'tomorrow'));
        }

        $overdueTasks = Task::where('status', '!=', 'done')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now()->toDateString())
            ->with('user')
            ->get();

        foreach ($overdueTasks as $task) {
            $alreadyNotified = $task->user->notifications()
                ->where('data->task_id', $task->id)
                ->where('data->reminder_type', 'overdue')
                ->whereDate('created_at', now()->toDateString())
                ->exists();

            if (!$alreadyNotified) {
                $task->user->notify(new DeadlineReminderNotification($task, 'overdue'));
            }
        }

        $total = $tasksDueTomorrow->count() + $overdueTasks->count();
        $this->info("Sent {$total} deadline reminders.");

        return self::SUCCESS;
    }
}
