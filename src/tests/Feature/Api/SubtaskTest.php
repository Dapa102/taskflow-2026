<?php

use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
    $this->headers = ['Authorization' => "Bearer {$this->token}"];
    $this->task = Task::factory()->create(['user_id' => $this->user->id]);
});

describe('Subtask CRUD', function () {
    it('lists subtasks for a task', function () {
        Subtask::factory()->count(3)->create(['task_id' => $this->task->id]);

        $response = $this->getJson("/api/tasks/{$this->task->id}/subtasks", $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['status', 'data', 'progress']);
    });

    it('creates a subtask', function () {
        $response = $this->postJson("/api/tasks/{$this->task->id}/subtasks", [
            'title' => 'Step 1: Research',
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Step 1: Research')
            ->assertJsonPath('data.is_completed', false);
    });

    it('rejects subtask without title', function () {
        $response = $this->postJson("/api/tasks/{$this->task->id}/subtasks", [], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    });

    it('updates subtask title', function () {
        $subtask = Subtask::factory()->create(['task_id' => $this->task->id]);

        $response = $this->putJson("/api/subtasks/{$subtask->id}", [
            'title' => 'Updated Step',
        ], $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Step');
    });

    it('toggles subtask completion', function () {
        $subtask = Subtask::factory()->create([
            'task_id' => $this->task->id,
            'is_completed' => false,
        ]);

        $response = $this->patchJson("/api/subtasks/{$subtask->id}/toggle", [], $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.is_completed', true);

        $response = $this->patchJson("/api/subtasks/{$subtask->id}/toggle", [], $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.is_completed', false);
    });

    it('deletes a subtask', function () {
        $subtask = Subtask::factory()->create(['task_id' => $this->task->id]);

        $response = $this->deleteJson("/api/subtasks/{$subtask->id}", [], $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('subtasks', ['id' => $subtask->id]);
    });

    it('returns 403 when accessing another user task subtasks', function () {
        $otherUser = User::factory()->create();
        $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/tasks/{$otherTask->id}/subtasks", $this->headers);

        $response->assertStatus(403);
    });
});

describe('Progress Calculation', function () {
    it('calculates progress correctly', function () {
        Subtask::factory()->count(2)->create(['task_id' => $this->task->id, 'is_completed' => true]);
        Subtask::factory()->count(2)->create(['task_id' => $this->task->id, 'is_completed' => false]);

        $response = $this->getJson("/api/tasks/{$this->task->id}/subtasks", $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('progress', 50);
    });

    it('returns 0 progress when no subtasks', function () {
        $response = $this->getJson("/api/tasks/{$this->task->id}/subtasks", $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('progress', 0);
    });

    it('returns 100 progress when all completed', function () {
        Subtask::factory()->count(3)->create(['task_id' => $this->task->id, 'is_completed' => true]);

        $response = $this->getJson("/api/tasks/{$this->task->id}/subtasks", $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('progress', 100);
    });

    it('updates progress after toggle', function () {
        Subtask::factory()->create(['task_id' => $this->task->id, 'is_completed' => false]);
        $subtask2 = Subtask::factory()->create(['task_id' => $this->task->id, 'is_completed' => false]);

        $response = $this->patchJson("/api/subtasks/{$subtask2->id}/toggle", [], $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('progress', 50);
    });
});
