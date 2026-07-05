<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Task;
use Livewire\Attributes\Layout;

#[Layout('layouts.super-admin')]
class ManageWorkspaces extends Component
{
    public $name = '';
    public $description = '';
    public $pmId = '';
    public $editId = null;
    public $editName = '';
    public $editDesc = '';
    public $editPmId = '';

    protected $rules = [
        'name' => 'required|string|max:100',
        'description' => 'nullable|string',
        'pmId' => 'required|exists:users,id',
    ];

    public function create()
    {
        $this->validate();

        Workspace::create([
            'pm_id' => $this->pmId,
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Workspace berhasil dibuat.');
        $this->reset(['name', 'description', 'pmId']);
    }

    public function edit($id)
    {
        $ws = Workspace::findOrFail($id);
        $this->editId = $ws->id;
        $this->editName = $ws->name;
        $this->editDesc = $ws->description ?? '';
        $this->editPmId = (string) $ws->pm_id;
    }

    public function update()
    {
        $this->validate([
            'editName' => 'required|string|max:100',
            'editDesc' => 'nullable|string',
            'editPmId' => 'required|exists:users,id',
        ]);

        $ws = Workspace::findOrFail($this->editId);
        $ws->update([
            'name' => $this->editName,
            'description' => $this->editDesc,
            'pm_id' => $this->editPmId,
        ]);

        session()->flash('message', 'Workspace diperbarui.');
        $this->reset(['editId', 'editName', 'editDesc', 'editPmId']);
    }

    public function delete($id)
    {
        Workspace::findOrFail($id)->delete();
        session()->flash('message', 'Workspace dihapus.');
    }

    public function render()
    {
        $workspaces = Workspace::with('pm', 'members')->latest()->get()->map(fn($ws) => [
            'id' => $ws->id,
            'name' => $ws->name,
            'description' => $ws->description,
            'pm' => $ws->pm,
            'member_count' => $ws->members->count(),
            'project_count' => $ws->projects()->count(),
            'task_count' => $ws->tasks()->count(),
            'created_at' => $ws->created_at,
        ]);

        $pms = User::where('role', 'pm')->where('is_active', true)->get();

        return view('livewire.super-admin.manage-workspaces', [
            'workspaces' => $workspaces,
            'pms' => $pms,
        ]);
    }
}
