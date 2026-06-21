<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Task;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.admin')]
class AssignTask extends Component
{
    public $title = '';
    public $description = '';
    public $priority = 'medium';
    public $deadline = '';
    public $selectedPm = '';
    public $selectedWorkspace = '';

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'nullable|max:1000',
        'priority' => 'required|in:low,medium,high',
        'deadline' => 'nullable|date|after_or_equal:today',
        'selectedPm' => 'required|exists:users,id',
        'selectedWorkspace' => 'required|exists:workspaces,id',
    ];

    public function updatedSelectedPm($value)
    {
        $this->selectedWorkspace = '';
    }

    public function assign()
    {
        $this->validate();

        $workspace = Workspace::findOrFail($this->selectedWorkspace);

        Task::create([
            'workspace_id' => $workspace->id,
            'created_by' => auth()->id(),
            'assigned_to' => $workspace->pm_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => 'todo',
            'priority' => $this->priority,
            'deadline' => $this->deadline ?: null,
        ]);

        session()->flash('message', 'Task assigned to PM successfully.');
        $this->reset(['title', 'description', 'priority', 'deadline', 'selectedPm', 'selectedWorkspace']);
    }

    public function render()
    {
        $pms = User::where('role', 'pm')->get();
        $workspaces = collect();
        if ($this->selectedPm) {
            $workspaces = Workspace::where('pm_id', $this->selectedPm)->get();
        }

        return view('livewire.admin.assign-task', [
            'pms' => $pms,
            'workspaces' => $workspaces,
        ]);
    }
}
