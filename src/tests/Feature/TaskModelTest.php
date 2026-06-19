<?php

use App\Models\Task;
use App\Models\User;

describe('Task Model', function () {
    it('belongs to a user', function () {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        expect($task->user->id)->toBe($user->id);
    });

    it('casts deadline to date', function () {
        $task = Task::factory()->create(['deadline' => '2026-07-01']);

        expect($task->deadline)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('detects overdue task', function () {
        $task = Task::factory()->overdue()->create();

        expect($task->isOverdue())->toBeTrue();
    });

    it('does not flag done task as overdue', function () {
        $task = Task::factory()->create([
            'deadline' => now()->subDays(3),
            'status' => 'done',
        ]);

        expect($task->isOverdue())->toBeFalse();
    });

    it('does not flag future deadline as overdue', function () {
        $task = Task::factory()->create([
            'deadline' => now()->addDays(5),
            'status' => 'todo',
        ]);

        expect($task->isOverdue())->toBeFalse();
    });
});

describe('Task Scopes', function () {
    it('filters by status', function () {
        Task::factory()->count(2)->todo()->create();
        Task::factory()->count(3)->done()->create();

        $result = Task::byStatus('todo')->get();
        expect($result)->toHaveCount(2);
    });

    it('returns all when status is null', function () {
        Task::factory()->count(5)->create();

        $result = Task::byStatus(null)->get();
        expect($result)->toHaveCount(5);
    });

    it('searches by title', function () {
        Task::factory()->create(['title' => 'Belajar Laravel']);
        Task::factory()->create(['title' => 'Kerjakan PR']);

        $result = Task::search('Laravel')->get();
        expect($result)->toHaveCount(1);
    });

    it('returns all when search is null', function () {
        Task::factory()->count(3)->create();

        $result = Task::search(null)->get();
        expect($result)->toHaveCount(3);
    });
});

describe('User-Task Relationship', function () {
    it('user has many tasks', function () {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $user->id]);

        expect($user->tasks)->toHaveCount(3);
    });

    it('cascade deletes tasks when user is deleted', function () {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $user->delete();

        expect(Task::where('user_id', $user->id)->count())->toBe(0);
    });
});
