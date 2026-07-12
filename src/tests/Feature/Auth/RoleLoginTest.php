<?php

use App\Models\User;

beforeEach(function () {
    $this->superAdmin = User::factory()->create([
        'name' => 'Super Admin',
        'email' => 'super@admin.com',
        'password' => bcrypt('password'),
        'role' => 'super_admin',
        'is_active' => true,
    ]);

    $this->pm = User::factory()->create([
        'name' => 'Project Manager',
        'email' => 'pm@test.com',
        'password' => bcrypt('password'),
        'role' => 'pm',
        'is_active' => true,
    ]);

    $this->member = User::factory()->create([
        'name' => 'Team Member',
        'email' => 'member@test.com',
        'password' => bcrypt('password'),
        'role' => 'member',
        'is_active' => true,
    ]);
});

describe('Web Login', function () {
    it('renders login page with new layout (no old guest layout)', function () {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign in');
        $response->assertSee('Soulmatters');
        $response->assertDontSee('Selamat Datang');
        $response->assertDontSee('bg-gray-100');
    });

    it('super_admin can login via web and redirects to dashboard', function () {
        $response = $this->post('/login', [
            'email' => 'super@admin.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($this->superAdmin);
        $response->assertRedirect(route('dashboard', absolute: false));
    });

    it('pm can login via web and redirects to dashboard', function () {
        $response = $this->post('/login', [
            'email' => 'pm@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($this->pm);
        $response->assertRedirect(route('dashboard', absolute: false));
    });

    it('member can login via web and redirects to dashboard', function () {
        $response = $this->post('/login', [
            'email' => 'member@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($this->member);
        $response->assertRedirect(route('dashboard', absolute: false));
    });

    it('rejects invalid credentials', function () {
        $response = $this->post('/login', [
            'email' => 'super@admin.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    });

    it('rejects non-existent email', function () {
        $response = $this->post('/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    });

    it('requires email and password', function () {
        $response = $this->post('/login', []);

        $this->assertGuest();
    });

    it('user can logout', function () {
        $this->actingAs($this->superAdmin);
        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    });
});

describe('API Login', function () {
    it('super_admin can login via API', function () {
        $response = $this->postJson('/api/login', [
            'email' => 'super@admin.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'user' => ['id', 'name', 'email', 'roles'], 'token'])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.roles', ['super_admin']);
    });

    it('pm can login via API', function () {
        $response = $this->postJson('/api/login', [
            'email' => 'pm@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'user' => ['id', 'name', 'email', 'roles'], 'token'])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.roles', ['pm']);
    });

    it('member can login via API', function () {
        $response = $this->postJson('/api/login', [
            'email' => 'member@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'user' => ['id', 'name', 'email', 'roles'], 'token'])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.roles', ['member']);
    });

    it('rejects invalid API credentials', function () {
        $response = $this->postJson('/api/login', [
            'email' => 'super@admin.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Invalid credentials');
    });

    it('rejects empty API login fields', function () {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422);
    });
});

describe('Dashboard Redirect', function () {
    it('super_admin redirected to super-admin dashboard', function () {
        $this->actingAs($this->superAdmin);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('super-admin.dashboard'));
    });

    it('pm redirected to pm dashboard', function () {
        $this->actingAs($this->pm);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('pm.dashboard'));
    });

    it('member redirected to member dashboard', function () {
        $this->actingAs($this->member);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('member.dashboard'));
    });
});

describe('Role Middleware', function () {
    it('prevents pm from accessing super-admin routes', function () {
        $this->actingAs($this->pm);

        $response = $this->get(route('super-admin.dashboard'));

        $response->assertStatus(403);
    });

    it('prevents member from accessing pm routes', function () {
        $this->actingAs($this->member);

        $response = $this->get(route('pm.dashboard'));

        $response->assertStatus(403);
    });

    it('allows super_admin to access admin routes', function () {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('super-admin.dashboard'));

        $response->assertStatus(200);
    });

    it('redirects unauthenticated to login', function () {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    });
});
