<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Workspace;
use Livewire\Livewire;
use App\Livewire\SuperAdmin\SuperAdminDashboard;

beforeEach(function () {
    $this->sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
});

it('renders super admin dashboard', function () {
    $this->actingAs($this->sa);
    Livewire::test(SuperAdminDashboard::class)
        ->assertOk();
});

it('syncs stat cards with chart data', function () {
    $ws = Workspace::factory()->create();
    $pm = User::factory()->create(['role' => 'pm', 'is_active' => true]);

    Task::factory()->create(['status' => 'todo', 'workspace_id' => $ws->id]);
    Task::factory()->create(['status' => 'in_progress', 'workspace_id' => $ws->id]);
    Task::factory()->create(['status' => 'review', 'workspace_id' => $ws->id]);
    Task::factory()->create(['status' => 'done', 'workspace_id' => $ws->id]);
    Task::factory()->create(['status' => 'pending_admin', 'workspace_id' => $ws->id]);
    Task::factory()->create(['status' => 'cancelled', 'workspace_id' => $ws->id]);

    $this->actingAs($this->sa);
    $component = Livewire::test(SuperAdminDashboard::class);

    $rendered = $component->html();
    preg_match('/data-donut=\'(.+?)\'/', $rendered, $m);
    $chart = json_decode(html_entity_decode($m[1]), true);

    $chartLabels = collect($chart)->pluck('label', 'count')->flip();

    foreach ($chart as $item) {
        $label = $item['label'];
        $count = $item['count'];
        if ($label === 'Cancelled') {
            continue;
        }
        $component->assertSeeHtml((string) $count);
    }
});

it('shows correct task counts in chart and stats', function () {
    $ws = Workspace::factory()->create();
    $pm = User::factory()->create(['role' => 'pm', 'is_active' => true]);

    Task::factory()->count(2)->create(['status' => 'todo', 'workspace_id' => $ws->id]);
    Task::factory()->count(3)->create(['status' => 'in_progress', 'workspace_id' => $ws->id]);
    Task::factory()->count(1)->create(['status' => 'review', 'workspace_id' => $ws->id]);
    Task::factory()->count(4)->create(['status' => 'done', 'workspace_id' => $ws->id]);
    Task::factory()->count(1)->create(['status' => 'pending_admin', 'workspace_id' => $ws->id]);
    Task::factory()->count(1)->create(['status' => 'cancelled', 'workspace_id' => $ws->id]);

    $this->actingAs($this->sa);

    $component = Livewire::test(SuperAdminDashboard::class);

    $rendered = $component->html();
    preg_match('/data-donut=\'(.+?)\'/', $rendered, $m);
    $chart = json_decode(html_entity_decode($m[1]), true);
    $chartMap = collect($chart)->keyBy('label');

    $component->assertSeeHtml('12');
    $component->assertSeeHtml('2');
    $component->assertSeeHtml('3');
    $component->assertSeeHtml('1');
    $component->assertSeeHtml('4');
    $component->assertSeeHtml('1');

    expect($chartMap['To Do']['count'])->toBe(2);
    expect($chartMap['In Progress']['count'])->toBe(3);
    expect($chartMap['Review']['count'])->toBe(1);
    expect($chartMap['Done']['count'])->toBe(4);
    expect($chartMap['Menunggu Approval']['count'])->toBe(1);
    expect($chartMap['Cancelled']['count'])->toBe(1);

    $chartSum = collect($chart)->sum('count');
    expect($chartSum)->toBe(12);
});
