<?php

namespace App\Livewire\Pm;

use Livewire\Component;
use App\Models\Task;
use Livewire\Attributes\Layout;

#[Layout('layouts.pm')]
class TaskDetail extends Component
{
    public Task $task;
    public $comment = '';

    protected $rules = [
        'comment' => 'required|min:1|max:2000',
    ];

    public function addComment()
    {
        $this->validate();

        $this->task->comments()->create([
            'user_id' => auth()->id(),
            'content' => $this->comment,
        ]);

        $this->comment = '';
        $this->dispatch('comment-added');
    }

    public function render()
    {
        $this->task->load(['comments.user', 'assignedMember', 'creator', 'project', 'statusHistories' => fn ($q) => $q->latest()->limit(10)]);

        return view('livewire.pm.task-detail', [
            'task' => $this->task,
        ]);
    }
}
