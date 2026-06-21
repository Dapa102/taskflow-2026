<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Task;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PmDashboard extends Component
{
    public $workspaceName;
    public $workspaceDesc;
    
    public $inviteEmail;
    
    public $taskTitle;
    public $taskDesc;
    public $taskAssignee;
    public $taskPriority = 'medium';
    public $taskDeadline;

    public function createWorkspace()
    {
        $this->validate([
            'workspaceName' => 'required|string|max:100',
            'workspaceDesc' => 'nullable|string',
        ]);

        if (auth()->user()->workspace) {
            session()->flash('error', 'You already have a workspace.');
            return;
        }

        Workspace::create([
            'pm_id' => auth()->id(),
            'name' => $this->workspaceName,
            'description' => $this->workspaceDesc,
        ]);

        session()->flash('message', 'Workspace created successfully.');
        $this->reset(['workspaceName', 'workspaceDesc']);
    }

    public function inviteMember()
    {
        $this->validate([
            'inviteEmail' => 'required|email|exists:users,email',
        ]);

        $workspace = auth()->user()->workspace;
        if (!$workspace) {
            session()->flash('error', 'Create a workspace first.');
            return;
        }

        $user = User::where('email', $this->inviteEmail)->where('role', 'member')->first();
        if (!$user) {
            session()->flash('error', 'User not found or is not a member role.');
            return;
        }

        if ($workspace->members()->where('user_id', $user->id)->exists()) {
            session()->flash('error', 'User is already in your workspace.');
            return;
        }

        $workspace->members()->attach($user->id);
        session()->flash('message', 'Member added successfully.');
        $this->reset('inviteEmail');
    }

    public function removeMember($userId)
    {
        $workspace = auth()->user()->workspace;
        if ($workspace) {
            $workspace->members()->detach($userId);
            session()->flash('message', 'Member removed.');
        }
    }

    public function createTask()
    {
        $workspace = auth()->user()->workspace;
        if (!$workspace) return;

        $this->validate([
            'taskTitle' => 'required|string|max:255',
            'taskDesc' => 'nullable|string',
            'taskAssignee' => 'required|exists:users,id',
            'taskPriority' => 'required|in:low,medium,high',
            'taskDeadline' => 'nullable|date|after_or_equal:today',
        ]);

        // Verify assignee is in workspace
        if (!$workspace->members()->where('user_id', $this->taskAssignee)->exists()) {
            session()->flash('error', 'Assignee must be a workspace member.');
            return;
        }

        Task::create([
            'workspace_id' => $workspace->id,
            'created_by' => auth()->id(),
            'assigned_to' => $this->taskAssignee,
            'title' => $this->taskTitle,
            'description' => $this->taskDesc,
            'priority' => $this->taskPriority,
            'deadline' => $this->taskDeadline,
        ]);

        session()->flash('message', 'Task created.');
        $this->reset(['taskTitle', 'taskDesc', 'taskAssignee', 'taskPriority', 'taskDeadline']);
    }

    public function deleteTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task) {
            $this->authorize('delete', $task);
            $task->delete();
            session()->flash('message', 'Task deleted.');
        }
    }

    public function render()
    {
        $workspace = auth()->user()->workspace;
        $members = $workspace ? $workspace->members : [];
        $tasks = $workspace ? $workspace->tasks()->with('assignee')->latest()->get() : [];
        
        $stats = [
            'total' => $tasks->count(),
            'done' => $tasks->where('status', 'done')->count(),
            'overdue' => $tasks->filter(fn($t) => $t->deadline && $t->deadline < now() && $t->status !== 'done')->count()
        ];

        return view('livewire.pm.pm-dashboard', [
            'workspace' => $workspace,
            'members' => $members,
            'tasks' => $tasks,
            'stats' => $stats
        ]);
    }
}
