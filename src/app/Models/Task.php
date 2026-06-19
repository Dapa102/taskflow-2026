<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'category_id',
        'title',
        'description',
        'status',
        'priority',
        'deadline',
    ];

    protected $attributes = [
        'status' => 'todo',
        'priority' => 'medium',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date:Y-m-d',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        return $this->hasMany(Comment::class)->oldest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignees')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function getProgressAttribute(): int
    {
        $total = $this->subtasks()->count();

        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->subtasks()->where('is_completed', true)->count() / $total) * 100);
    }

    public function scopeByStatus($query, ?string $status)
    {
        if ($status) {
            $query->where('status', $status);
        }
    }

    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }
    }

    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== 'done';
    }
}
