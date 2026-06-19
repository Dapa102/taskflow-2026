<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->id === $task->user_id) return true;
        if ($task->team_id && $task->team->hasMember($user)) return true;
        return $user->can('view_any_task');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->id === $task->user_id) return true;
        if ($task->team_id && $task->team->hasMember($user)) return true;
        return $user->can('update_any_task');
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->id === $task->user_id) return true;
        return $user->can('delete_any_task');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_task');
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Task $task): bool
    {
        return $user->can('restore_task');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_task');
    }

    public function replicate(User $user, Task $task): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
