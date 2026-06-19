<?php

use App\Models\Team;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
    $this->headers = ['Authorization' => "Bearer {$this->token}"];
});

describe('Team CRUD', function () {
    it('lists teams for authenticated user', function () {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->create(['user_id' => $this->user->id, 'role' => 'admin', 'joined_at' => now()]);

        $response = $this->getJson('/api/teams', $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    });

    it('creates a team and auto-adds owner as admin', function () {
        $response = $this->postJson('/api/teams', [
            'name' => 'Tim Developer',
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Tim Developer')
            ->assertJsonPath('data.owner_id', $this->user->id);

        $this->assertDatabaseHas('team_members', [
            'team_id' => $response->json('data.id'),
            'user_id' => $this->user->id,
            'role' => 'admin',
        ]);
    });

    it('requires name when creating team', function () {
        $response = $this->postJson('/api/teams', [], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    });

    it('shows team detail with owner, members, and tasks', function () {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->create(['user_id' => $this->user->id, 'role' => 'admin', 'joined_at' => now()]);

        $response = $this->getJson("/api/teams/{$team->id}", $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', $team->name);
    });

    it('returns 403 when viewing another users team', function () {
        $otherUser = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $otherUser->id]);

        $response = $this->getJson("/api/teams/{$team->id}", $this->headers);

        $response->assertStatus(403);
    });

    it('updates team name', function () {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->create(['user_id' => $this->user->id, 'role' => 'admin', 'joined_at' => now()]);

        $response = $this->putJson("/api/teams/{$team->id}", [
            'name' => 'Tim Baru',
        ], $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Tim Baru');
    });

    it('deletes team', function () {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);

        $response = $this->deleteJson("/api/teams/{$team->id}", [], $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    });
});

describe('Team Members', function () {
    it('lists team members', function () {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $member = User::factory()->create();
        $team->members()->create(['user_id' => $this->user->id, 'role' => 'admin', 'joined_at' => now()]);
        $team->members()->create(['user_id' => $member->id, 'role' => 'member', 'joined_at' => now()]);

        $response = $this->getJson("/api/teams/{$team->id}/members", $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    });

    it('adds member to team', function () {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->create(['user_id' => $this->user->id, 'role' => 'admin', 'joined_at' => now()]);
        $newMember = User::factory()->create();

        $response = $this->postJson("/api/teams/{$team->id}/members", [
            'user_id' => $newMember->id,
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'member');
    });

    it('rejects adding duplicate member', function () {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->create(['user_id' => $this->user->id, 'role' => 'admin', 'joined_at' => now()]);

        $response = $this->postJson("/api/teams/{$team->id}/members", [
            'user_id' => $this->user->id,
        ], $this->headers);

        $response->assertStatus(409);
    });

    it('joins team via invite code', function () {
        $team = Team::factory()->create(['owner_id' => $this->user->id, 'invite_code' => 'TEST1234']);
        $team->members()->create(['user_id' => $this->user->id, 'role' => 'admin', 'joined_at' => now()]);
        $newUser = User::factory()->create();
        $newToken = $newUser->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/teams/join', [
            'invite_code' => 'TEST1234',
        ], ['Authorization' => "Bearer {$newToken}"]);

        $response->assertStatus(201);
    });

    it('rejects invalid invite code', function () {
        $response = $this->postJson('/api/teams/join', [
            'invite_code' => 'INVALID',
        ], $this->headers);

        $response->assertStatus(404);
    });

    it('removes member from team', function () {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->create(['user_id' => $this->user->id, 'role' => 'admin', 'joined_at' => now()]);
        $member = User::factory()->create();
        $memberRecord = $team->members()->create(['user_id' => $member->id, 'role' => 'member', 'joined_at' => now()]);

        $response = $this->deleteJson("/api/teams/{$team->id}/members/{$memberRecord->id}", [], $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('team_members', ['id' => $memberRecord->id]);
    });
});
