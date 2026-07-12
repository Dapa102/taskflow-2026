<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Workspace;
use Livewire\Livewire;
use App\Livewire\Pm\PmDashboard;

beforeEach(function () {
    $this->pm = User::factory()->create(['role' => 'pm', 'is_active' => true]);
    $ws = Workspace::factory()->create(['pm_id' => $this->pm->id]);
    $ws->members()->attach($this->pm->id);

    Task::factory()->count(2)->create([
        'assigned_pm_id' => $this->pm->id,
        'workspace_id' => $ws->id,
        'status' => 'todo',
    ]);
    Task::factory()->count(3)->create([
        'assigned_pm_id' => $this->pm->id,
        'workspace_id' => $ws->id,
        'status' => 'in_progress',
    ]);
    Task::factory()->count(1)->create([
        'assigned_pm_id' => $this->pm->id,
        'workspace_id' => $ws->id,
        'status' => 'review',
    ]);
    Task::factory()->count(4)->create([
        'assigned_pm_id' => $this->pm->id,
        'workspace_id' => $ws->id,
        'status' => 'done',
    ]);
    Task::factory()->count(1)->create([
        'assigned_pm_id' => $this->pm->id,
        'workspace_id' => $ws->id,
        'status' => 'cancelled',
    ]);
});

it('renders PM dashboard', function () {
    $this->actingAs($this->pm);
    Livewire::test(PmDashboard::class)
        ->assertOk();
});

it('syncs stat cards with chart data', function () {
    $this->actingAs($this->pm);
    $component = Livewire::test(PmDashboard::class);

    $rendered = $component->html();
    preg_match('/data-donut=\'(.+?)\'/', $rendered, $m);
    $chart = json_decode(html_entity_decode($m[1]), true);
    $chartMap = collect($chart)->keyBy('label');

    $component->assertSeeHtml('Total Tasks');
    $component->assertSeeHtml('11');

    expect($chartMap['To Do']['count'])->toBe(2);
    expect($chartMap['In Progress']['count'])->toBe(3);
    expect($chartMap['Review']['count'])->toBe(1);
    expect($chartMap['Done']['count'])->toBe(4);
    if (isset($chartMap['Cancelled'])) {
        expect($chartMap['Cancelled']['count'])->toBe(1);
    }

    $chartSum = collect($chart)->sum('count');
    expect($chartSum)->toBe(11);
});
