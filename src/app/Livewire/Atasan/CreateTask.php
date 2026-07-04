<?php

namespace App\Livewire\Atasan;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use App\Services\TaskStatusHistoryService;
use Livewire\Attributes\Layout;

#[Layout('layouts.atasan')]
class CreateTask extends Component
{
    public $title = '';
    public $description = '';
    public $priority = 'medium';
    public $deadline = '';
    public $workspaceId = '';
    public $recommendedPmId = '';

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'nullable|max:2000',
        'priority' => 'required|in:low,medium,high',
        'deadline' => 'nullable|date|after_or_equal:today',
        'workspaceId' => 'nullable|exists:workspaces,id',
        'recommendedPmId' => 'nullable|exists:users,id',
    ];

    public function save()
    {
        $this->validate();

        $task = Task::create([
            'workspace_id' => $this->workspaceId ?: null,
            'created_by' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'status' => 'draft',
            'priority' => $this->priority,
            'deadline' => $this->deadline ?: null,
            'recommended_pm_id' => $this->recommendedPmId ?: null,
        ]);

        app(TaskStatusHistoryService::class)->record(
            $task, 'draft', 'draft', 'Tugas dibuat oleh Super Admin'
        );

        session()->flash('message', 'Tugas berhasil dibuat.');
        $this->reset(['title', 'description', 'priority', 'deadline', 'workspaceId', 'recommendedPmId']);
    }

    public function render()
    {
        $workspaces = Workspace::with('pm')->latest()->get();
        $pms = User::where('role', 'pm')->where('is_active', true)->get();

        return view('livewire.atasan.create-task', [
            'workspaces' => $workspaces,
            'pms' => $pms,
        ]);
    }
}
