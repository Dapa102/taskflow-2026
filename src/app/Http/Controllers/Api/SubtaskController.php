<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SubtaskController extends Controller
{
    public function index(Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        $subtasks = $task->subtasks()->orderBy('created_at')->get();

        return response()->json([
            'status' => 'success',
            'data' => $subtasks,
            'progress' => $task->progress,
        ]);
    }

    public function store(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('update', $task);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $subtask = $task->subtasks()->create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $subtask,
            'progress' => $task->progress,
        ], 201);
    }

    public function update(Request $request, Subtask $subtask): JsonResponse
    {
        Gate::authorize('update', $subtask->task);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $subtask->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $subtask->fresh(),
        ]);
    }

    public function toggle(Subtask $subtask): JsonResponse
    {
        Gate::authorize('update', $subtask->task);

        $subtask->update(['is_completed' => !$subtask->is_completed]);

        return response()->json([
            'status' => 'success',
            'data' => $subtask->fresh(),
            'progress' => $subtask->task->progress,
        ]);
    }

    public function destroy(Subtask $subtask): JsonResponse
    {
        Gate::authorize('update', $subtask->task);

        $taskId = $subtask->task_id;
        $subtask->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Subtask deleted',
            'progress' => Task::find($taskId)->progress,
        ]);
    }
}
