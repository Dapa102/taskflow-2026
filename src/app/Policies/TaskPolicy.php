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
        if ($user->role === 'admin') return true;
        if ($user->role === 'pm') {
            return $task->workspace && $task->workspace->pm_id === $user->id;
        }
        return $task->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'pm' && $user->workspace !== null;
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->role === 'admin') return false;
        if ($user->role === 'pm') {
            return $task->workspace && $task->workspace->pm_id === $user->id;
        }
        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->role === 'admin') return false;
        if ($user->role === 'pm') {
            return $task->workspace && $task->workspace->pm_id === $user->id;
        }
        return false;
    }

    public function changeStatus(User $user, Task $task): bool
    {
        return $task->assigned_to === $user->id && $user->role === 'member';
    }
}
