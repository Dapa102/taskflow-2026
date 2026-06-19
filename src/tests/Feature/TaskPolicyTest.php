<?php

use App\Models\Task;
use App\Models\User;

describe('TaskPolicy', function () {
    it('allows owner to view task', function () {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        expect($user->can('view', $task))->toBeTrue();
    });

    it('denies non-owner from viewing task', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        expect($user->can('view', $task))->toBeFalse();
    });

    it('allows owner to update task', function () {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        expect($user->can('update', $task))->toBeTrue();
    });

    it('denies non-owner from updating task', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        expect($user->can('update', $task))->toBeFalse();
    });

    it('allows owner to delete task', function () {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        expect($user->can('delete', $task))->toBeTrue();
    });

    it('denies non-owner from deleting task', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        expect($user->can('delete', $task))->toBeFalse();
    });
});
