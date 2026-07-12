<?php

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;

describe('TaskPolicy', function () {
    it('allows super admin to view any task', function () {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $task = Task::factory()->create();

        expect($admin->can('view', $task))->toBeTrue();
    });

    it('allows pm to view task in their workspace', function () {
        $pm = User::factory()->create(['role' => 'pm']);
        $workspace = Workspace::factory()->create(['pm_id' => $pm->id]);
        $task = Task::factory()->create(['workspace_id' => $workspace->id]);

        expect($pm->can('view', $task))->toBeTrue();
    });

    it('denies pm from viewing task in another workspace', function () {
        $pm = User::factory()->create(['role' => 'pm']);
        $otherPm = User::factory()->create(['role' => 'pm']);
        $workspace = Workspace::factory()->create(['pm_id' => $otherPm->id]);
        $task = Task::factory()->create(['workspace_id' => $workspace->id]);

        expect($pm->can('view', $task))->toBeFalse();
    });

    it('allows member to view their assigned task', function () {
        $member = User::factory()->create(['role' => 'member']);
        $task = Task::factory()->create(['assigned_member_id' => $member->id]);

        expect($member->can('view', $task))->toBeTrue();
    });

    it('denies member from viewing unassigned task', function () {
        $member = User::factory()->create(['role' => 'member']);
        $otherMember = User::factory()->create(['role' => 'member']);
        $task = Task::factory()->create(['assigned_member_id' => $otherMember->id]);

        expect($member->can('view', $task))->toBeFalse();
    });

    it('allows only pm to create task', function () {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $pm = User::factory()->create(['role' => 'pm']);
        $member = User::factory()->create(['role' => 'member']);

        expect($admin->can('create', Task::class))->toBeFalse();
        expect($pm->can('create', Task::class))->toBeTrue();
        expect($member->can('create', Task::class))->toBeFalse();
    });

    it('allows super admin to update any task', function () {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $task = Task::factory()->create();

        expect($admin->can('update', $task))->toBeTrue();
    });

    it('allows pm to update task in their workspace', function () {
        $pm = User::factory()->create(['role' => 'pm']);
        $workspace = Workspace::factory()->create(['pm_id' => $pm->id]);
        $task = Task::factory()->create(['workspace_id' => $workspace->id]);

        expect($pm->can('update', $task))->toBeTrue();
    });

    it('denies pm from updating task in another workspace', function () {
        $pm = User::factory()->create(['role' => 'pm']);
        $otherPm = User::factory()->create(['role' => 'pm']);
        $workspace = Workspace::factory()->create(['pm_id' => $otherPm->id]);
        $task = Task::factory()->create(['workspace_id' => $workspace->id]);

        expect($pm->can('update', $task))->toBeFalse();
    });

    it('denies member from updating task', function () {
        $member = User::factory()->create(['role' => 'member']);
        $task = Task::factory()->create();

        expect($member->can('update', $task))->toBeFalse();
    });

    it('allows super admin to delete any task', function () {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $task = Task::factory()->create();

        expect($admin->can('delete', $task))->toBeTrue();
    });

    it('allows pm to delete task in their workspace', function () {
        $pm = User::factory()->create(['role' => 'pm']);
        $workspace = Workspace::factory()->create(['pm_id' => $pm->id]);
        $task = Task::factory()->create(['workspace_id' => $workspace->id]);

        expect($pm->can('delete', $task))->toBeTrue();
    });

    it('denies pm from deleting task in another workspace', function () {
        $pm = User::factory()->create(['role' => 'pm']);
        $otherPm = User::factory()->create(['role' => 'pm']);
        $workspace = Workspace::factory()->create(['pm_id' => $otherPm->id]);
        $task = Task::factory()->create(['workspace_id' => $workspace->id]);

        expect($pm->can('delete', $task))->toBeFalse();
    });
});
