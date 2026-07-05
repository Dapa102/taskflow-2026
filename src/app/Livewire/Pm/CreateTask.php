<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskStatusHistoryService;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class CreateTask extends Component
{
    public $projectId = '';
    public $title = '';
    public $description = '';
    public $priority = 'medium';
    public $deadline = '';
    public $assignMemberId = '';

    protected $rules = [
        'projectId' => 'required|exists:projects,id',
        'title' => 'required|min:3|max:255',
        'description' => 'nullable|max:2000',
        'priority' => 'required|in:low,medium,high',
        'deadline' => 'nullable|date|after_or_equal:today',
        'assignMemberId' => 'required|exists:users,id',
    ];

    public function save()
    {
        $this->validate();

        $workspace = auth()->user()->currentWorkspace();
        if (!$workspace) {
            session()->flash('error', 'Belum punya workspace.');
            return;
        }

        $task = Task::create([
            'workspace_id' => $workspace->id,
            'project_id' => $this->projectId,
            'created_by' => auth()->id(),
            'assigned_pm_id' => auth()->id(),
            'assigned_member_id' => $this->assignMemberId,
            'assigned_to' => $this->assignMemberId,
            'title' => $this->title,
            'description' => $this->description,
            'status' => 'assigned_member',
            'priority' => $this->priority,
            'deadline' => $this->deadline ?: null,
        ]);

        app(TaskStatusHistoryService::class)->record(
            $task, 'assigned_member', 'assigned_member', 'Tugas dibuat oleh PM dan ditugaskan ke anggota'
        );

        session()->flash('message', 'Tugas berhasil dibuat dan ditugaskan.');
        $this->reset(['projectId', 'title', 'description', 'priority', 'deadline', 'assignMemberId']);
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace();

        $projects = collect();
        $members = collect();

        if ($workspace) {
            try {
                $projects = $workspace->projects()->where('status', 'active')->latest()->get();
            } catch (\Exception $e) {
                $projects = collect();
            }

            try {
                $members = $workspace->members()->latest()->get();
            } catch (\Exception $e) {
                $members = collect();
            }
        }

        return view('livewire.pm.create-task', [
            'workspace' => $workspace,
            'projects' => $projects,
            'members' => $members,
        ]);
    }
}
