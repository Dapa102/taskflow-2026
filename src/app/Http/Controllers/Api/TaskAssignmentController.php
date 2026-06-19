<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskAssignmentController extends Controller
{
    public function index(Task $task): JsonResponse
    {
        Gate::authorize("view", $task);

        $assignees = $task->assignees()->get();

        return response()->json([
            "status" => "success",
            "data" => $assignees,
        ]);
    }

    public function assign(Request $request, Task $task): JsonResponse
    {
        Gate::authorize("update", $task);

        $validated = $request->validate([
            "user_id" => ["required", "exists:users,id"],
        ]);

        $user = User::findOrFail($validated["user_id"]);

        if ($task->team_id) {
            $team = $task->team;
            if (!$team->hasMember($user)) {
                return response()->json([
                    "status" => "error",
                    "message" => "User is not a member of the task team",
                ], 403);
            }
        }

        if ($task->assignees()->where("user_id", $user->id)->exists()) {
            return response()->json([
                "status" => "error",
                "message" => "User is already assigned to this task",
            ], 409);
        }

        $task->assignees()->attach($user->id, ["assigned_at" => now()]);

        // Notify assigned user
        $user->notify(new TaskAssignedNotification($task, $request->user()));

        return response()->json([
            "status" => "success",
            "message" => "User assigned to task",
        ]);
    }

    public function unassign(Task $task, User $user): JsonResponse
    {
        Gate::authorize("update", $task);

        if (!$task->assignees()->where("user_id", $user->id)->exists()) {
            return response()->json([
                "status" => "error",
                "message" => "User is not assigned to this task",
            ], 404);
        }

        $task->assignees()->detach($user->id);

        return response()->json([
            "status" => "success",
            "message" => "User unassigned from task",
        ]);
    }

    public function myTasks(Request $request): JsonResponse
    {
        $tasks = $request->user()->assignedTasks()
            ->with("category")
            ->paginate(20);

        return response()->json([
            "status" => "success",
            "data" => $tasks->items(),
            "meta" => [
                "current_page" => $tasks->currentPage(),
                "last_page" => $tasks->lastPage(),
                "total" => $tasks->total(),
            ],
        ]);
    }
}
