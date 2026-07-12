<?php

namespace App\Livewire\Member;

use App\Enums\TaskStatus;
use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Services\TaskStatusHistoryService;
use Carbon\Carbon;

#[Layout('layouts.member')]
class MemberDashboard extends Component
{
    use WithFileUploads;

    public $upload = [];
    public $uploadingTaskId;

    public $detailTaskModal = false;
    public $detailTask = null;

    public function startTask($taskId): void
    {
        $task = Task::where('assigned_member_id', auth()->id())
            ->where('status', TaskStatus::TODO)
            ->where('status', '!=', TaskStatus::DONE)
            ->findOrFail($taskId);

        app(TaskStatusHistoryService::class)->transition(
            $task, TaskStatus::IN_PROGRESS, 'Tugas mulai dikerjakan oleh anggota'
        );

        session()->flash('message', 'Status tugas diubah menjadi In Progress.');
    }

    public function submitTask($taskId)
    {
        $this->validate([
            "upload.{$taskId}" => 'required|file|max:10240|mimes:pdf,doc,docx,zip,xlsx,xls,jpg,jpeg,png',
        ]);

        $task = Task::where('assigned_member_id', auth()->id())
            ->whereIn('status', [TaskStatus::TODO, TaskStatus::IN_PROGRESS])
            ->where('status', '!=', TaskStatus::DONE)
            ->findOrFail($taskId);

        $file = $this->upload[$taskId];
        $path = $file->store('task-submissions', 'public');

        $task->attachments()->create([
            'user_id' => auth()->id(),
            'filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        app(TaskStatusHistoryService::class)->transition(
            $task, TaskStatus::REVIEW, 'Tugas diserahkan oleh anggota untuk review'
        );

        $task->update(['submitted_at' => now()]);

        session()->flash('message', 'Tugas selesai dikerjakan. Menunggu review PM.');
        $this->reset('upload');
    }

    public function showTaskDetail($taskId)
    {
        $this->detailTask = Task::with(['workspace', 'assignedPm', 'creator', 'attachments', 'comments.user'])
            ->where('assigned_member_id', auth()->id())
            ->findOrFail($taskId);

        $this->detailTaskModal = true;
    }

    public function render()
    {
        $tasks = Task::where('assigned_member_id', auth()->id())
            ->with(['workspace', 'attachments', 'creator', 'assignedPm'])
            ->latest()
            ->get();

        $pm = User::where('role', 'pm')
            ->whereHas('workspaces', function ($q) {
                $q->whereHas('members', fn ($q2) => $q2->where('user_id', auth()->id()));
            })
            ->first();

        $myTeams = \App\Models\TeamMember::where('user_id', auth()->id())
            ->with('team.owner')
            ->get();

        $total = $tasks->count();
        $done = $tasks->where('status', TaskStatus::DONE)->count();
        $belumSelesai = $total - $done;
        $deadlineCount = $tasks->filter(fn($t) => $t->deadline && $t->status !== TaskStatus::DONE)->count();
        $reviewCount = $tasks->where('status', TaskStatus::REVIEW)->count();

        $chartData = [
            ['label' => 'To Do', 'count' => $tasks->where('status', TaskStatus::TODO)->count(), 'bg' => '#cbd5e1'],
            ['label' => 'In Progress', 'count' => $tasks->where('status', TaskStatus::IN_PROGRESS)->count(), 'bg' => '#94a3b8'],
            ['label' => 'Review', 'count' => $reviewCount, 'bg' => '#64748b'],
            ['label' => 'Done', 'count' => $done, 'bg' => '#1e293b'],
            ['label' => 'Cancelled', 'count' => $tasks->where('status', TaskStatus::CANCELLED)->count(), 'bg' => '#334155'],
        ];

        $doneTasks = Task::where('assigned_member_id', auth()->id())
            ->where('status', TaskStatus::DONE)
            ->where('updated_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->get()
            ->groupBy(fn($t) => $t->updated_at->format('Y-m-d'));

        $dailyChartData = [];
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $key = $date->format('Y-m-d');
            $count = isset($doneTasks[$key]) ? $doneTasks[$key]->count() : 0;
            $dailyChartData[] = [
                'label' => $dayNames[$date->dayOfWeek],
                'count' => $count,
                'bg' => $date->isToday() ? '#1e293b' : '#94a3b8',
            ];
        }

        return view('livewire.member.member-dashboard', [
            'tasks' => $tasks,
            'pm' => $pm,
            'myTeams' => $myTeams,
            'total' => $total,
            'done' => $done,
            'deadlineCount' => $deadlineCount,
            'revisionCount' => $reviewCount,
            'chartData' => $chartData,
            'dailyChartData' => $dailyChartData,
        ]);
    }
}
