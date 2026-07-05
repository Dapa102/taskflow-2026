<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->role === 'super_admin') return true;
        if ($user->role === 'pm') {
            return $task->workspace && $task->workspace->pm_id === $user->id;
        }
        return $task->assigned_member_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'pm']);
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->role === 'super_admin') return true;
        if ($user->role === 'pm') {
            return $task->workspace && $task->workspace->pm_id === $user->id;
        }
        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->role === 'super_admin') return true;
        if ($user->role === 'pm') {
            return $task->workspace && $task->workspace->pm_id === $user->id;
        }
        return false;
    }

    public function changeStatus(User $user, Task $task): bool
    {
        return $task->assigned_member_id === $user->id && $user->role === 'member';
    }
}
