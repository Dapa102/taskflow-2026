<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Workspace;
use Livewire\Livewire;
use App\Livewire\SuperAdmin\MemberPerformance;

beforeEach(function () {
    $this->sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
    $this->member = User::factory()->create(['role' => 'member', 'is_active' => true]);
    $ws = Workspace::factory()->create();
    $ws->members()->attach($this->member->id);
    Task::factory()->count(2)->create([
        'assigned_member_id' => $this->member->id,
        'workspace_id' => $ws->id,
        'status' => 'done',
    ]);
    Task::factory()->create([
        'assigned_member_id' => $this->member->id,
        'workspace_id' => $ws->id,
        'status' => 'in_progress',
        'deadline' => now()->subDays(1),
    ]);
});

it('renders member performance page', function () {
    $this->actingAs($this->sa);
    Livewire::test(MemberPerformance::class)
        ->assertOk()
        ->assertSee($this->member->name);
});

it('shows member metrics correctly', function () {
    $this->actingAs($this->sa);
    Livewire::test(MemberPerformance::class)
        ->assertSee('2')
        ->assertSee('1');
});

it('calls exportPdf without error', function () {
    $this->actingAs($this->sa);
    Livewire::test(MemberPerformance::class)
        ->call('exportPdf')
        ->assertOk();
});

it('downloads member performance PDF', function () {
    $this->actingAs($this->sa);
    $response = $this->get(route('export.member-performance'));
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
});
