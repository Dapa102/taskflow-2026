<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $attributes = [
        'status' => 'todo',
        'priority' => 'medium',
    ];

    protected $fillable = [
        'workspace_id',
        'project_id',
        'user_id',
        'created_by',
        'assigned_to',
        'recommended_pm_id',
        'assigned_pm_id',
        'assigned_member_id',
        'reviewed_by',
        'team_id',
        'title',
        'description',
        'review_note',
        'status',
        'priority',
        'deadline',
        'submitted_at',
        'escalated_at',
        'revision_counter',
        'max_revision_limit',
        'cancellation_note',
        'arbitration_decision',
        'file_path',
        'file_original_name',
    ];

    protected $casts = [
        'deadline' => 'date:Y-m-d',
        'submitted_at' => 'datetime',
        'escalated_at' => 'datetime',
        'revision_counter' => 'integer',
        'max_revision_limit' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('title', 'like', '%' . $search . '%');
        }
        return $query;
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function recommendedPm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_pm_id');
    }

    public function assignedPm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_pm_id');
    }

    public function assignedMember(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_member_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TaskStatusHistory::class);
    }

    public function inboxNotifications(): HasMany
    {
        return $this->hasMany(InboxNotification::class);
    }

    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast()
            && !in_array($this->status, [TaskStatus::DONE, TaskStatus::CANCELLED, TaskStatus::REVIEW, TaskStatus::PENDING_ADMIN]);
    }

    public function isRevisiLocked(): bool
    {
        return $this->revision_counter >= $this->max_revision_limit;
    }

    public function getStatusLabelAttribute(): string
    {
        return TaskStatus::label($this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return TaskStatus::badgeClass($this->status);
    }

    public function getProgressAttribute(): ?string
    {
        return null;
    }
}
