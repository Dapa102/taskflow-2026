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
        $query = $request->user()->tasks()
            ->with('category')
            ->byStatus($request->query('status'))
            ->search($request->query('search'));

        if ($request->has('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        $tasks = $query->orderByRaw('deadline IS NULL, deadline ASC')->get();

        return response()->json([
            'status' => 'success',
            'data' => $tasks,
        ]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $request->user()->tasks()->create(
            $request->validated()
        );

        return response()->json([
            'status' => 'success',
            'data' => $task,
        ], 201);
    }

    public function show(Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        $task->load('category');

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
}
