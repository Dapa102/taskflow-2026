<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'nomor_whatsapp',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class, 'pm_id');
    }

    public function currentWorkspace()
    {
        return $this->workspaces()->first();
    }

    public function memberWorkspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_members', 'user_id', 'workspace_id')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'user_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function pmTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_pm_id');
    }

    public function memberTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_member_id');
    }

    public function recommendedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'recommended_pm_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TaskStatusHistory::class, 'changed_by');
    }

    public function inboxNotifications(): HasMany
    {
        return $this->hasMany(InboxNotification::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isPm(): bool
    {
        return $this->role === 'pm';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }
}
