<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Project;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class Projects extends Component
{
    public $name = '';
    public $description = '';
    public $deadline = '';
    public $showForm = false;

    protected $rules = [
        'name' => 'required|string|max:200',
        'description' => 'nullable|string',
        'deadline' => 'nullable|date|after_or_equal:today',
    ];

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        $this->reset(['name', 'description', 'deadline']);
    }

    public function create()
    {
        $this->validate();

        $workspace = auth()->user()->currentWorkspace();
        if (!$workspace) {
            session()->flash('error', 'Belum punya workspace.');
            return;
        }

        Project::create([
            'workspace_id' => $workspace->id,
            'name' => $this->name,
            'description' => $this->description,
            'deadline' => $this->deadline ?: null,
            'created_by' => auth()->id(),
        ]);

        session()->flash('message', 'Project berhasil dibuat.');
        $this->reset(['name', 'description', 'deadline', 'showForm']);
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace();
        $projects = $workspace
            ? $workspace->projects()->withCount('tasks')->latest()->get()
            : collect();

        return view('livewire.pm.projects', [
            'workspace' => $workspace,
            'projects' => $projects,
        ]);
    }
}
