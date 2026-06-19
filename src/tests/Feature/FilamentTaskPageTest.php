<?php

use App\Models\Comment;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Filament\Admin\Resources\TaskResource\Pages\EditTask;
use App\Filament\Admin\Resources\TaskResource\Pages\ListTasks;
use Livewire\Livewire;

it('can render the task list page', function () {
    $user = User::factory()->create();
    Task::factory()->count(3)->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(ListTasks::class)->assertSuccessful();
});

it('can render the task edit page with subtasks and comments', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    Subtask::factory()->count(3)->create(['task_id' => $task->id]);
    Comment::factory()->count(2)->create(['task_id' => $task->id, 'user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(EditTask::class, ['record' => $task->id])->assertSuccessful();
});
