<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\InboxNotification;
use Illuminate\Console\Command;

class CheckPmEscalation extends Command
{
    protected $signature = 'tasks:check-pm-escalation';

    protected $description = 'Escalate pending_pm tasks where PM exceeds 48h review window';

    public function handle(): int
    {
        $cutoff = now()->subHours(48);

        $tasks = Task::where('status', 'pending_pm')
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '<', $cutoff)
            ->whereNull('escalated_at')
            ->get();

        $count = 0;
        foreach ($tasks as $task) {
            $task->update(['escalated_at' => now()]);

            InboxNotification::create([
                'user_id' => $task->created_by,
                'task_id' => $task->id,
                'channel' => 'inbox',
                'subject' => 'Eskalasi PM: Tugas Menunggu Review',
                'message' => "Tugas \"{$task->title}\" sudah >48 jam menunggu review PM ({$task->assignedPm?->name}). Tugas otomatis dieskalasi ke Super Admin.",
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $count++;
        }

        $this->info("Escalated {$count} tasks to Super Admin.");
        return self::SUCCESS;
    }
}
