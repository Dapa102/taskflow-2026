<?php

use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
    $this->headers = ['Authorization' => "Bearer {$this->token}"];
});

describe('Category CRUD', function () {
    it('lists categories for authenticated user', function () {
        Category::factory()->count(3)->create(['user_id' => $this->user->id]);
        Category::factory()->count(2)->create();

        $response = $this->getJson('/api/categories', $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    });

    it('creates a category', function () {
        $response = $this->postJson('/api/categories', [
            'name' => 'Pekerjaan',
            'color' => '#EF4444',
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Pekerjaan')
            ->assertJsonPath('data.color', '#EF4444');
    });

    it('creates a category with default color', function () {
        $response = $this->postJson('/api/categories', [
            'name' => 'Kuliah',
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.color', '#3B82F6');
    });

    it('rejects category without name', function () {
        $response = $this->postJson('/api/categories', [
            'color' => '#EF4444',
        ], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    });

    it('updates a category', function () {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Name',
        ], $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name');
    });

    it('returns 403 when updating another user category', function () {
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Hacked',
        ], $this->headers);

        $response->assertStatus(403);
    });

    it('deletes a category without deleting associated tasks', function () {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $task = \App\Models\Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->deleteJson("/api/categories/{$category->id}", [], $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'category_id' => null]);
    });

    it('returns 403 when deleting another user category', function () {
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/categories/{$category->id}", [], $this->headers);

        $response->assertStatus(403);
    });
});

describe('Task with Category', function () {
    it('filters tasks by category_id', function () {
        $cat1 = Category::factory()->create(['user_id' => $this->user->id]);
        $cat2 = Category::factory()->create(['user_id' => $this->user->id]);

        \App\Models\Task::factory()->count(2)->create(['user_id' => $this->user->id, 'category_id' => $cat1->id]);
        \App\Models\Task::factory()->count(3)->create(['user_id' => $this->user->id, 'category_id' => $cat2->id]);

        $response = $this->getJson("/api/tasks?category_id={$cat1->id}", $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    });

    it('creates task with category', function () {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson('/api/tasks', [
            'title' => 'Categorized Task',
            'category_id' => $category->id,
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.category_id', $category->id);
    });

    it('eager loads category in task list', function () {
        $category = Category::factory()->create(['user_id' => $this->user->id, 'name' => 'Work']);
        \App\Models\Task::factory()->create(['user_id' => $this->user->id, 'category_id' => $category->id]);

        $response = $this->getJson('/api/tasks', $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.0.category.name', 'Work');
    });
});
