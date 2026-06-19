<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Team;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Team $team): bool
    {
        if ($team->isOwner($user)) return true;
        if ($team->hasMember($user)) return true;
        return $user->can('view_any_team');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Team $team): bool
    {
        if ($team->isOwner($user)) return true;
        return $user->can('update_any_team');
    }

    public function delete(User $user, Team $team): bool
    {
        if ($team->isOwner($user)) return true;
        return $user->can('delete_any_team');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_team');
    }

    public function forceDelete(User $user, Team $team): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Team $team): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, Team $team): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
