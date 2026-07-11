<?php

namespace App\Livewire\Member;

use App\Enums\TaskStatus;
use Livewire\Component;
use App\Models\Task;
use App\Services\TaskStatusHistoryService;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[Layout('layouts.member')]
class Tasks extends Component
{
    use WithFileUploads;

    public $upload = [];
    public $detailModal = false;
    public $detailTitle = '';
    public $detailTasks = [];

    public function startTask($taskId): void
    {
        $task = Task::where('assigned_member_id', auth()->id())
            ->where('status', TaskStatus::TODO)
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

        session()->flash('message', 'Tugas berhasil dikirim untuk direview PM.');
        $this->reset('upload');
    }

    public function showDetail($taskId)
    {
        $task = Task::with(['workspace', 'assignedPm', 'creator', 'attachments', 'comments.user'])
            ->where('assigned_member_id', auth()->id())
            ->findOrFail($taskId);

        $this->detailTitle = $task->title;
        $this->detailTasks = [$task];
        $this->detailModal = true;
    }

    public function render()
    {
        $tasks = Task::where('assigned_member_id', auth()->id())
            ->with(['project', 'workspace', 'assignedPm', 'creator', 'attachments'])
            ->latest()
            ->get();

        return view('livewire.member.tasks', compact('tasks'));
    }
}
