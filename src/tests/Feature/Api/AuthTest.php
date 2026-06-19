<?php

use App\Models\User;

describe('Register', function () {
    it('registers a new user successfully', function () {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['status', 'user' => ['id', 'name', 'email'], 'token'])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.email', 'test@example.com');

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    });

    it('rejects registration with duplicate email', function () {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    });

    it('rejects registration with empty fields', function () {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    });

    it('rejects password shorter than 8 characters', function () {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    });
});

describe('Login', function () {
    it('logs in with valid credentials', function () {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'user' => ['id', 'name', 'email'], 'token'])
            ->assertJsonPath('status', 'success');
    });

    it('rejects login with invalid credentials', function () {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Invalid credentials');
    });
});

describe('Logout', function () {
    it('logs out and revokes token', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/logout', [], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Logged out');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    });

    it('rejects unauthenticated logout', function () {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    });
});

describe('User Profile', function () {
    it('returns authenticated user data', function () {
        $user = User::factory()->create();

        $response = $this->getJson('/api/user', [
            'Authorization' => "Bearer {$user->createToken('test')->plainTextToken}",
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $user->id);
    });
});
