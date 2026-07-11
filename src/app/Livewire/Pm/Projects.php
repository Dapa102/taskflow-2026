<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;
use App\Enums\TaskStatus;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class Projects extends Component
{
    public $name = '';
    public $description = '';
    public $deadline = '';
    public $showForm = false;
    public $projectDetailModal = false;
    public $projectDetail = null;

    public $showEditModal = false;
    public $editProjectId;
    public $editName;
    public $editDescription;
    public $editDeadline;

    public function editProject($id)
    {
        $project = Project::findOrFail($id);
        $this->editProjectId = $project->id;
        $this->editName = $project->name;
        $this->editDescription = $project->description;
        $this->editDeadline = $project->deadline?->format('Y-m-d');
        $this->showEditModal = true;
    }

    public function updateProject()
    {
        $this->validate([
            'editName' => 'required|string|max:200',
            'editDescription' => 'nullable|string',
            'editDeadline' => 'nullable|date',
        ]);

        Project::findOrFail($this->editProjectId)->update([
            'name' => $this->editName,
            'description' => $this->editDescription,
            'deadline' => $this->editDeadline ?: null,
        ]);

        session()->flash('message', 'Project berhasil diperbarui.');
        $this->reset(['editProjectId', 'editName', 'editDescription', 'editDeadline', 'showEditModal']);
    }

    public function deleteProject($id)
    {
        $project = Project::findOrFail($id);
        $project->tasks()->delete();
        $project->delete();
        session()->flash('message', 'Project berhasil dihapus.');
    }

    public function showProjectDetail($projectId)
    {
        $this->projectDetail = Project::with(['workspace', 'creator'])
            ->withCount('tasks')
            ->findOrFail($projectId);
        $this->projectDetail->done_count = Task::where('project_id', $projectId)
            ->where('status', TaskStatus::DONE)->count();
        $this->projectDetailModal = true;
    }

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
