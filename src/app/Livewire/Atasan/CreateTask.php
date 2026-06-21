<?php

namespace App\Livewire\Atasan;

use Livewire\Component;
use App\Models\Task;
use App\Models\Workspace;
use Livewire\Attributes\Layout;

#[Layout('layouts.atasan')]
class CreateTask extends Component
{
    public $title = '';
    public $description = '';
    public $priority = 'medium';
    public $deadline = '';
    public $workspaceId = '';

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'nullable|max:2000',
        'priority' => 'required|in:low,medium,high',
        'deadline' => 'nullable|date|after_or_equal:today',
        'workspaceId' => 'required|exists:workspaces,id',
    ];

    public function save()
    {
        $this->validate();

        Task::create([
            'workspace_id' => $this->workspaceId,
            'created_by' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'status' => 'todo',
            'priority' => $this->priority,
            'deadline' => $this->deadline ?: null,
        ]);

        session()->flash('message', 'Tugas berhasil dibuat dan dikirim ke Super Admin.');
        $this->reset(['title', 'description', 'priority', 'deadline', 'workspaceId']);
    }

    public function render()
    {
        $workspaces = Workspace::with('pm')->latest()->get();

        return view('livewire.atasan.create-task', [
            'workspaces' => $workspaces,
        ]);
    }
}
