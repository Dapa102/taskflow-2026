<?php

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
    $this->headers = ['Authorization' => "Bearer {$this->token}"];
    $this->task = Task::factory()->create(['user_id' => $this->user->id]);
});

describe('Comment CRUD', function () {
    it('lists comments for a task', function () {
        Comment::factory()->count(3)->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/tasks/{$this->task->id}/comments", $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    });

    it('creates a comment', function () {
        $response = $this->postJson("/api/tasks/{$this->task->id}/comments", [
            'content' => 'Kendala: data belum masuk dari tim finance.',
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.content', 'Kendala: data belum masuk dari tim finance.')
            ->assertJsonPath('data.user_id', $this->user->id);
    });

    it('rejects comment without content', function () {
        $response = $this->postJson("/api/tasks/{$this->task->id}/comments", [], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('content');
    });

    it('includes user data in comment list', function () {
        Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/tasks/{$this->task->id}/comments", $this->headers);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['user' => ['id', 'name', 'email']]]]);
    });

    it('deletes own comment', function () {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/comments/{$comment->id}", [], $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    });

    it('returns 403 when deleting another user comment', function () {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $otherUser->id,
        ]);

        $response = $this->deleteJson("/api/comments/{$comment->id}", [], $this->headers);

        $response->assertStatus(403);
    });

    it('returns 403 when accessing another user task comments', function () {
        $otherUser = User::factory()->create();
        $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/tasks/{$otherTask->id}/comments", $this->headers);

        $response->assertStatus(403);
    });

    it('orders comments chronologically (oldest first)', function () {
        Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'content' => 'First comment',
            'created_at' => now()->subHours(2),
        ]);
        Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'content' => 'Second comment',
            'created_at' => now()->subHour(),
        ]);

        $response = $this->getJson("/api/tasks/{$this->task->id}/comments", $this->headers);

        $response->assertStatus(200);
        $contents = collect($response->json('data'))->pluck('content')->toArray();
        expect($contents[0])->toBe('First comment');
        expect($contents[1])->toBe('Second comment');
    });
});
