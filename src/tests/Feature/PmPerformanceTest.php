<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Workspace;
use Livewire\Livewire;
use App\Livewire\SuperAdmin\PmPerformance;

beforeEach(function () {
    $this->sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
    $this->pm = User::factory()->create(['role' => 'pm', 'is_active' => true]);
    $ws = Workspace::factory()->create(['pm_id' => $this->pm->id]);
    $ws->members()->attach($this->pm->id);
    Task::factory()->count(3)->create([
        'assigned_pm_id' => $this->pm->id,
        'workspace_id' => $ws->id,
        'status' => 'done',
    ]);
    Task::factory()->create([
        'assigned_pm_id' => $this->pm->id,
        'workspace_id' => $ws->id,
        'status' => 'in_progress',
        'deadline' => now()->subDays(2),
    ]);
});

it('renders PM performance page', function () {
    $this->actingAs($this->sa);
    Livewire::test(PmPerformance::class)
        ->assertOk()
        ->assertSee($this->pm->name);
});

it('shows PM metrics correctly', function () {
    $this->actingAs($this->sa);
    Livewire::test(PmPerformance::class)
        ->assertSee('3')
        ->assertSee('1');
});

it('exports PM performance PDF', function () {
    $this->actingAs($this->sa);
    Livewire::test(PmPerformance::class)
        ->call('exportPdf')
        ->assertFileDownloaded('pm-performance.pdf');
});
