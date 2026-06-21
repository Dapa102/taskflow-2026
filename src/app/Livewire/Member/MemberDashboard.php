<?php

namespace App\Livewire\Member;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Models\Attachment;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[Layout('layouts.member')]
class MemberDashboard extends Component
{
    use WithFileUploads;

    public $upload = [];
    public $uploadingTaskId;

    public function submitTask($taskId)
    {
        $this->validate([
            "upload.{$taskId}" => 'required|file|max:10240|mimes:pdf,doc,docx,zip,xlsx,xls,jpg,jpeg,png',
        ]);

        $task = Task::where('assigned_to', auth()->id())->findOrFail($taskId);

        $file = $this->upload[$taskId];
        $path = $file->store('task-submissions', 'public');

        $task->attachments()->create([
            'user_id' => auth()->id(),
            'filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        $task->update(['status' => 'pending_pm']);

        session()->flash('message', 'Tugas selesai dikerjakan. Menunggu review PM.');
        $this->reset('upload');
    }

    public function render()
    {
        $tasks = Task::where('assigned_to', auth()->id())
            ->with('workspace', 'attachments', 'creator')
            ->latest()
            ->get();

        $pm = User::where('role', 'pm')
            ->whereHas('workspace', function ($q) {
                $q->whereHas('members', fn ($q2) => $q2->where('user_id', auth()->id()));
            })
            ->first();

        $myTeams = \App\Models\TeamMember::where('user_id', auth()->id())
            ->with('team.owner')
            ->get();

        return view('livewire.member.member-dashboard', [
            'tasks' => $tasks,
            'pm' => $pm,
            'myTeams' => $myTeams,
        ]);
    }
}
