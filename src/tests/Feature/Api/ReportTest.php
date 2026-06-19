<?php

use App\Models\Task;
use App\Models\Team;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
    $this->headers = ['Authorization' => "Bearer {$this->token}"];

    Task::factory()->count(3)->create(['user_id' => $this->user->id, 'status' => 'todo', 'deadline' => null]);
    Task::factory()->count(2)->create(['user_id' => $this->user->id, 'status' => 'on_progress', 'deadline' => null]);
    Task::factory()->count(5)->create(['user_id' => $this->user->id, 'status' => 'done', 'deadline' => null]);
});

describe('Report Summary', function () {
    it('returns summary stats for current user', function () {
        $response = $this->getJson('/api/reports/summary', $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.total', 10)
            ->assertJsonPath('data.by_status.todo', 3)
            ->assertJsonPath('data.by_status.on_progress', 2)
            ->assertJsonPath('data.by_status.done', 5)
            ->assertJsonPath('data.completion_rate', 50);
    });

    it('returns 0 completion rate when no tasks exist', function () {
        $newUser = User::factory()->create();
        $newToken = $newUser->createToken('test')->plainTextToken;

        $response = $this->getJson('/api/reports/summary', [
            'Authorization' => "Bearer {$newToken}",
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.total', 0)
            ->assertJsonPath('data.completion_rate', 0);
    });

    it('includes overdue count', function () {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'todo',
            'deadline' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/reports/summary', $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.overdue', 1);
    });
});

describe('Team Stats', function () {
    it('returns team stats', function () {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->create(['user_id' => $this->user->id, 'role' => 'admin', 'joined_at' => now()]);

        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'team_id' => $team->id,
            'status' => 'done',
        ]);

        $response = $this->getJson("/api/reports/team/{$team->id}?period=all", $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.period', 'all')
            ->assertJsonPath('data.team_name', $team->name)
            ->assertJsonPath('data.by_status.done', 3)
            ->assertJsonPath('data.completion_rate', 100);
    });

    it('returns 403 for team user does not belong to', function () {
        $otherUser = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $otherUser->id]);

        $response = $this->getJson("/api/reports/team/{$team->id}", $this->headers);

        $response->assertStatus(403);
    });
});

describe('Export', function () {
    it('exports tasks as CSV', function () {
        $response = $this->getJson('/api/reports/export', $this->headers);

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    });
});
