<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Task;
use App\Models\InboxNotification;
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
    public $editDeputyPmId = '';

    protected $rules = [
        'name' => 'required|string|max:100',
        'description' => 'nullable|string',
        'pmId' => 'required|exists:users,id',
    ];

    public function create()
    {
        $this->validate();

        $ws = Workspace::create([
            'pm_id' => $this->pmId,
            'name' => $this->name,
            'description' => $this->description,
        ]);

        InboxNotification::create([
            'user_id' => $this->pmId,
            'subject' => 'Ditunjuk sebagai Project Manager',
            'message' => "Anda ditunjuk sebagai Project Manager untuk workspace \"{$ws->name}\".",
            'channel' => 'inbox',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        session()->flash('message', 'Workspace berhasil dibuat.');
        $this->reset(['name', 'description', 'pmId']);
    }

    public function edit($id)
    {
        $ws = Workspace::with('deputyPm')->findOrFail($id);
        $this->editId = $ws->id;
        $this->editName = $ws->name;
        $this->editDesc = $ws->description ?? '';
        $this->editPmId = (string) $ws->pm_id;
        $this->editDeputyPmId = (string) ($ws->deputy_pm_id ?? '');
    }

    public function update()
    {
        $this->validate([
            'editName' => 'required|string|max:100',
            'editDesc' => 'nullable|string',
            'editPmId' => 'required|exists:users,id',
            'editDeputyPmId' => 'nullable|exists:users,id',
        ]);

        $ws = Workspace::findOrFail($this->editId);
        $oldPmId = $ws->pm_id;
        $oldDeputyId = $ws->deputy_pm_id;
        $ws->update([
            'name' => $this->editName,
            'description' => $this->editDesc,
            'pm_id' => $this->editPmId,
            'deputy_pm_id' => $this->editDeputyPmId ?: null,
        ]);

        if ($oldPmId != $this->editPmId) {
            InboxNotification::create([
                'user_id' => $this->editPmId,
                'subject' => 'Ditunjuk sebagai Project Manager',
                'message' => "Anda ditunjuk sebagai Project Manager untuk workspace \"{$ws->name}\".",
                'channel' => 'inbox',
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        if ($this->editDeputyPmId && (string) $oldDeputyId !== $this->editDeputyPmId) {
            InboxNotification::create([
                'user_id' => $this->editDeputyPmId,
                'subject' => 'Ditunjuk sebagai Deputy PM',
                'message' => "Anda ditunjuk sebagai Deputy Project Manager untuk workspace \"{$ws->name}\".",
                'channel' => 'inbox',
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        session()->flash('message', 'Workspace diperbarui.');
        $this->reset(['editId', 'editName', 'editDesc', 'editPmId', 'editDeputyPmId']);
    }

    public function delete($id)
    {
        Workspace::findOrFail($id)->delete();
        session()->flash('message', 'Workspace dihapus.');
    }

    public function render()
    {
        $workspaces = Workspace::with('pm', 'deputyPm', 'members')->latest()->get()->map(fn($ws) => [
            'id' => $ws->id,
            'name' => $ws->name,
            'description' => $ws->description,
            'pm' => $ws->pm,
            'deputy_pm' => $ws->deputyPm,
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
