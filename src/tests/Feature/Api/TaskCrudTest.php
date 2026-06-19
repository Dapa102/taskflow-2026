<?php

use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
    $this->headers = ['Authorization' => "Bearer {$this->token}"];
});

describe('Create Task', function () {
    it('creates a task with title only', function () {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.title', 'Test Task')
            ->assertJsonPath('data.status', 'todo')
            ->assertJsonPath('data.priority', 'medium');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $this->user->id,
        ]);
    });

    it('creates a task with all fields', function () {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Full Task',
            'description' => 'A detailed description',
            'status' => 'on_progress',
            'priority' => 'high',
            'deadline' => '2026-07-01',
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Full Task')
            ->assertJsonPath('data.description', 'A detailed description')
            ->assertJsonPath('data.status', 'on_progress')
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonPath('data.deadline', '2026-07-01');
    });

    it('rejects task without title', function () {
        $response = $this->postJson('/api/tasks', [
            'description' => 'No title here',
        ], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    });

    it('rejects task with invalid status', function () {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Bad Status',
            'status' => 'canceled',
        ], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('status');
    });

    it('rejects title longer than 255 characters', function () {
        $response = $this->postJson('/api/tasks', [
            'title' => str_repeat('a', 256),
        ], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    });

    it('rejects unauthenticated request', function () {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Unauth Task',
        ]);

        $response->assertStatus(401);
    });
});

describe('List Tasks', function () {
    it('returns all tasks for authenticated user', function () {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);
        Task::factory()->count(2)->create();

        $response = $this->getJson('/api/tasks', $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(3, 'data');
    });

    it('filters tasks by status', function () {
        Task::factory()->count(2)->todo()->create(['user_id' => $this->user->id]);
        Task::factory()->count(3)->done()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/tasks?status=todo', $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    });

    it('searches tasks by title', function () {
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Beli alat tulis']);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Kerjakan PR']);

        $response = $this->getJson('/api/tasks?search=alat', $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Beli alat tulis');
    });

    it('combines status filter and search', function () {
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Task A', 'status' => 'todo']);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Task B', 'status' => 'done']);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Other', 'status' => 'todo']);

        $response = $this->getJson('/api/tasks?status=todo&search=Task', $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    });

    it('sorts tasks by deadline ascending with nulls last', function () {
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'No deadline', 'deadline' => null]);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Far', 'deadline' => '2026-12-31']);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Soon', 'deadline' => '2026-07-01']);

        $response = $this->getJson('/api/tasks', $this->headers);

        $response->assertStatus(200);
        $titles = collect($response->json('data'))->pluck('title')->toArray();
        expect($titles[0])->toBe('Soon');
        expect($titles[1])->toBe('Far');
        expect($titles[2])->toBe('No deadline');
    });
});

describe('Show Task', function () {
    it('returns task detail', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/tasks/{$task->id}", $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $task->id);
    });

    it('returns 403 for another user task', function () {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/tasks/{$task->id}", $this->headers);

        $response->assertStatus(403);
    });
});

describe('Update Task', function () {
    it('updates task fields', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'status' => 'done',
            'priority' => 'high',
        ], $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Title')
            ->assertJsonPath('data.status', 'done')
            ->assertJsonPath('data.priority', 'high');
    });

    it('returns 403 when updating another user task', function () {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Hacked Title',
        ], $this->headers);

        $response->assertStatus(403);
    });
});

describe('Delete Task', function () {
    it('deletes own task', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}", [], $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Task deleted');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    });

    it('returns 403 when deleting another user task', function () {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}", [], $this->headers);

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    });
});
