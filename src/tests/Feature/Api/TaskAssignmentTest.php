<?php

use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
    $this->headers = ['Authorization' => "Bearer {$this->token}"];
    $this->task = Task::factory()->create(['user_id' => $this->user->id]);
});

describe('Task Assignment', function () {
    it('lists assignees for a task', function () {
        $this->task->assignees()->attach($this->user->id, ['assigned_at' => now()]);

        $response = $this->getJson("/api/tasks/{$this->task->id}/assignees", $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    });

    it('assigns user to task', function () {
        $assignee = User::factory()->create();

        $response = $this->postJson("/api/tasks/{$this->task->id}/assign", [
            'user_id' => $assignee->id,
        ], $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseHas('task_assignees', [
            'task_id' => $this->task->id,
            'user_id' => $assignee->id,
        ]);
    });

    it('rejects assigning same user twice', function () {
        $assignee = User::factory()->create();
        $this->task->assignees()->attach($assignee->id, ['assigned_at' => now()]);

        $response = $this->postJson("/api/tasks/{$this->task->id}/assign", [
            'user_id' => $assignee->id,
        ], $this->headers);

        $response->assertStatus(409);
    });

    it('unassigns user from task', function () {
        $assignee = User::factory()->create();
        $this->task->assignees()->attach($assignee->id, ['assigned_at' => now()]);

        $response = $this->deleteJson("/api/tasks/{$this->task->id}/assign/{$assignee->id}", [], $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('task_assignees', [
            'task_id' => $this->task->id,
            'user_id' => $assignee->id,
        ]);
    });

    it('returns 403 when assigning to another users task', function () {
        $otherUser = User::factory()->create();
        $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->postJson("/api/tasks/{$otherTask->id}/assign", [
            'user_id' => $this->user->id,
        ], $this->headers);

        $response->assertStatus(403);
    });

    it('lists my assigned tasks', function () {
        $task2 = Task::factory()->create(['user_id' => $this->user->id]);
        $this->task->assignees()->attach($this->user->id, ['assigned_at' => now()]);
        $task2->assignees()->attach($this->user->id, ['assigned_at' => now()]);

        $response = $this->getJson('/api/tasks/assigned', $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    });
});
