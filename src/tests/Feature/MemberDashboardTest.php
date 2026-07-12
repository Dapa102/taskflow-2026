<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Workspace;
use Livewire\Livewire;
use App\Livewire\Member\MemberDashboard;

beforeEach(function () {
    $this->member = User::factory()->create(['role' => 'member', 'is_active' => true]);
    $ws = Workspace::factory()->create();
    $ws->members()->attach($this->member->id);

    Task::factory()->count(2)->create([
        'assigned_member_id' => $this->member->id,
        'workspace_id' => $ws->id,
        'status' => 'todo',
    ]);
    Task::factory()->count(3)->create([
        'assigned_member_id' => $this->member->id,
        'workspace_id' => $ws->id,
        'status' => 'in_progress',
    ]);
    Task::factory()->count(1)->create([
        'assigned_member_id' => $this->member->id,
        'workspace_id' => $ws->id,
        'status' => 'review',
    ]);
    Task::factory()->count(4)->create([
        'assigned_member_id' => $this->member->id,
        'workspace_id' => $ws->id,
        'status' => 'done',
    ]);
    Task::factory()->count(1)->create([
        'assigned_member_id' => $this->member->id,
        'workspace_id' => $ws->id,
        'status' => 'cancelled',
    ]);
});

it('renders member dashboard', function () {
    $this->actingAs($this->member);
    Livewire::test(MemberDashboard::class)
        ->assertOk();
});

it('syncs chart data with task totals', function () {
    $this->actingAs($this->member);
    $component = Livewire::test(MemberDashboard::class);

    $rendered = $component->html();
    preg_match('/data-donut=\'(.+?)\'/', $rendered, $m);
    $chart = json_decode(html_entity_decode($m[1]), true);
    $chartMap = collect($chart)->keyBy('label');

    expect($chartMap['To Do']['count'])->toBe(2);
    expect($chartMap['In Progress']['count'])->toBe(3);
    expect($chartMap['Review']['count'])->toBe(1);
    expect($chartMap['Done']['count'])->toBe(4);
    expect($chartMap['Cancelled']['count'])->toBe(1);

    $chartSum = collect($chart)->sum('count');
    expect($chartSum)->toBe(11);
});
