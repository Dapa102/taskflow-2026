<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Task::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('team.members', fn ($mq) => $mq->where('user_id', $user->id));
        })->with('category');

        if ($request->has('team_id')) {
            $query->where('team_id', $request->query('team_id'));
        }

        $query->byStatus($request->query('status'))
              ->search($request->query('search'));

        if ($request->has('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        $perPage = $request->query('per_page', 100);
        $tasks = $query->orderByRaw('deadline IS NULL, deadline ASC')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $tasks->items(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'total' => $tasks->total(),
                'per_page' => $tasks->perPage(),
            ],
        ]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $teamId = $validated['team_id'] ?? null;

        if ($teamId) {
            $team = \App\Models\Team::findOrFail($teamId);
            Gate::authorize('view', $team);
        }

        $validated['created_by'] = $request->user()->id;

        $task = $request->user()->tasks()->create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $task,
        ], 201);
    }

    public function show(Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        $task->load('category');

        if ($includes = request()->query('include')) {
            $task->load(explode(',', $includes));
        }

        return response()->json([
            'status' => 'success',
            'data' => $task,
            'progress' => $task->progress,
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        Gate::authorize('update', $task);

        $task->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => $task->fresh(),
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted',
        ]);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:tasks,id'],
        ]);

        $tasks = Task::whereIn('id', $validated['ids'])->get();
        $count = 0;

        foreach ($tasks as $task) {
            if (Gate::allows('delete', $task)) {
                $task->delete();
                $count++;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "{$count} tasks deleted",
        ]);
    }

    public function bulkStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:tasks,id'],
            'status' => ['required', 'string', 'in:todo,on_progress,done'],
        ]);

        $tasks = Task::whereIn('id', $validated['ids'])->get();
        $count = 0;

        foreach ($tasks as $task) {
            if (Gate::allows('update', $task)) {
                $task->update(['status' => $validated['status']]);
                $count++;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "{$count} tasks updated to {$validated['status']}",
        ]);
    }
}
