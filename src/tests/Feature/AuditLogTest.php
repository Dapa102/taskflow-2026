<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Workspace;
use App\Models\AuditLog;
use App\Services\AuditService;
use Livewire\Livewire;
use App\Livewire\SuperAdmin\AuditLogs;

beforeEach(function () {
    $this->sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
});

it('logs a custom action via AuditService', function () {
    $log = app(AuditService::class)->log(
        action: 'test_action',
        description: 'Testing audit logging',
    );
    expect($log)->toBeInstanceOf(AuditLog::class);
    expect($log->action)->toBe('test_action');
});

it('logs task status change via transition', function () {
    $pm = User::factory()->create(['role' => 'pm']);
    $ws = Workspace::factory()->create(['pm_id' => $pm->id]);
    $task = Task::factory()->create([
        'assigned_pm_id' => $pm->id,
        'workspace_id' => $ws->id,
        'status' => 'todo',
    ]);

    $this->actingAs($pm);
    $service = app(\App\Services\TaskStatusHistoryService::class);
    $service->transition($task, 'in_progress');

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'task_in_progress',
        'entity_type' => Task::class,
        'entity_id' => $task->id,
        'user_id' => $pm->id,
    ]);
});

it('renders audit log page for SA', function () {
    AuditLog::factory()->count(3)->create();

    $this->actingAs($this->sa);
    Livewire::test(AuditLogs::class)
        ->assertOk();
});

it('filters audit logs by action', function () {
    AuditLog::factory()->create(['action' => 'task_done']);
    AuditLog::factory()->create(['action' => 'task_review']);

    $this->actingAs($this->sa);
    Livewire::test(AuditLogs::class)
        ->set('actionFilter', 'task_done')
        ->assertOk();
});
