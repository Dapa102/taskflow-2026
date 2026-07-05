<?php

namespace App\Livewire\Member;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Services\TaskStatusHistoryService;
use Carbon\Carbon;

#[Layout('layouts.member')]
class MemberDashboard extends Component
{
    use WithFileUploads;

    public $upload = [];
    public $uploadingTaskId;

    public $detailModal = false;
    public $detailTitle = '';
    public $detailTasks = [];

    public $detailTaskModal = false;
    public $detailTask = null;

    public function submitTask($taskId)
    {
        $this->validate([
            "upload.{$taskId}" => 'required|file|max:10240|mimes:pdf,doc,docx,zip,xlsx,xls,jpg,jpeg,png',
        ]);

        $task = Task::where('assigned_member_id', auth()->id())->findOrFail($taskId);

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
            $task, 'pending_pm', 'Tugas diserahkan oleh anggota'
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

    #[On('showDetail')]
    public function showDetail($label)
    {
        $this->detailTitle = $label;
        $statuses = match ($label) {
            'Dikerjakan' => ['assigned_member'],
            'Menunggu Review' => ['pending_pm'],
            'Revisi' => ['revision'],
            'Menunggu Approval' => ['pending_admin'],
            'Arbitrase' => ['pending_arbitration'],
            'Selesai' => ['done'],
            default => [],
        };

        $this->detailTasks = Task::with(['workspace', 'assignedPm', 'creator'])
            ->where('assigned_member_id', auth()->id())
            ->whereIn('status', $statuses)
            ->latest()
            ->get();

        $this->detailModal = true;
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
        $done = $tasks->where('status', 'done')->count();
        $belumSelesai = $total - $done;
        $deadlineCount = $tasks->filter(fn($t) => $t->deadline && $t->status !== 'done')->count();
        $revisionCount = $tasks->where('status', 'revision')->count();

        $chartData = [
            ['label' => 'Dikerjakan', 'count' => $tasks->where('status', 'assigned_member')->count(), 'bg' => '#6366f1'],
            ['label' => 'Menunggu Review', 'count' => $tasks->where('status', 'pending_pm')->count(), 'bg' => '#eab308'],
            ['label' => 'Revisi', 'count' => $revisionCount, 'bg' => '#f97316'],
            ['label' => 'Menunggu Approval', 'count' => $tasks->where('status', 'pending_admin')->count(), 'bg' => '#a855f7'],
            ['label' => 'Arbitrase', 'count' => $tasks->where('status', 'pending_arbitration')->count(), 'bg' => '#ef4444'],
            ['label' => 'Selesai', 'count' => $done, 'bg' => '#22c55e'],
        ];

        $doneTasks = Task::where('assigned_member_id', auth()->id())
            ->where('status', 'done')
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
                'bg' => $date->isToday() ? '#6366f1' : '#a5b4fc',
            ];
        }

        return view('livewire.member.member-dashboard', [
            'tasks' => $tasks,
            'pm' => $pm,
            'myTeams' => $myTeams,
            'total' => $total,
            'done' => $done,
            'deadlineCount' => $deadlineCount,
            'revisionCount' => $revisionCount,
            'chartData' => $chartData,
            'dailyChartData' => $dailyChartData,
        ]);
    }
}
