<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Workspace;
use Livewire\Livewire;
use App\Livewire\SuperAdmin\LateTasks;

beforeEach(function () {
    $this->sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
    $this->pm = User::factory()->create(['role' => 'pm', 'is_active' => true]);
    $ws = Workspace::factory()->create(['pm_id' => $this->pm->id]);
    Task::factory()->create([
        'assigned_pm_id' => $this->pm->id,
        'workspace_id' => $ws->id,
        'title' => 'Overdue Task A',
        'deadline' => now()->subDays(3),
        'status' => 'in_progress',
    ]);
    Task::factory()->create([
        'assigned_pm_id' => $this->pm->id,
        'workspace_id' => $ws->id,
        'title' => 'Future Task',
        'deadline' => now()->addDays(5),
        'status' => 'in_progress',
    ]);
});

it('renders late tasks page', function () {
    $this->actingAs($this->sa);
    Livewire::test(LateTasks::class)
        ->assertOk()
        ->assertSee('Overdue Task A')
        ->assertDontSee('Future Task');
});

it('filters by workspace', function () {
    $this->actingAs($this->sa);
    $ws2 = Workspace::factory()->create();
    Task::factory()->create([
        'workspace_id' => $ws2->id,
        'title' => 'Other Overdue',
        'deadline' => now()->subDay(),
        'status' => 'in_progress',
    ]);
    Livewire::test(LateTasks::class)
        ->set('workspaceFilter', $ws2->id)
        ->assertSee('Other Overdue')
        ->assertDontSee('Overdue Task A');
});

it('searches by title', function () {
    $this->actingAs($this->sa);
    Livewire::test(LateTasks::class)
        ->set('search', 'Overdue')
        ->assertSee('Overdue Task A')
        ->assertDontSee('Future Task');
});

it('exports late tasks PDF', function () {
    $this->actingAs($this->sa);
    Livewire::test(LateTasks::class)
        ->call('exportPdf')
        ->assertRedirect(route('export.late-tasks'));
});

it('downloads late tasks PDF via export route', function () {
    $this->actingAs($this->sa);
    $response = $this->get(route('export.late-tasks'));
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
});
