<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();

        $tasks = Task::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('team.members', fn ($mq) => $mq->where('user_id', $user->id));
        });

        $total = (clone $tasks)->count();
        $todo = (clone $tasks)->where('status', 'todo')->count();
        $onProgress = (clone $tasks)->where('status', 'on_progress')->count();
        $done = (clone $tasks)->where('status', 'done')->count();
        $overdue = (clone $tasks)->where('status', '!=', 'done')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now()->toDateString())
            ->count();
        $withDeadline = (clone $tasks)->whereNotNull('deadline')->count();

        $completionRate = $total > 0 ? round(($done / $total) * 100, 1) : 0;

        $today = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $createdToday = (clone $tasks)->whereDate('created_at', $today)->count();
        $createdThisWeek = (clone $tasks)->whereDate('created_at', '>=', $weekStart)->count();
        $createdThisMonth = (clone $tasks)->whereDate('created_at', '>=', $monthStart)->count();

        $doneToday = (clone $tasks)->where('status', 'done')->whereDate('updated_at', $today)->count();
        $doneThisWeek = (clone $tasks)->where('status', 'done')->whereDate('updated_at', '>=', $weekStart)->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $total,
                'by_status' => [
                    'todo' => $todo,
                    'on_progress' => $onProgress,
                    'done' => $done,
                ],
                'overdue' => $overdue,
                'with_deadline' => $withDeadline,
                'completion_rate' => $completionRate,
                'activity' => [
                    'created_today' => $createdToday,
                    'created_this_week' => $createdThisWeek,
                    'created_this_month' => $createdThisMonth,
                    'done_today' => $doneToday,
                    'done_this_week' => $doneThisWeek,
                ],
            ],
        ]);
    }

    public function teamStats(Request $request, Team $team): JsonResponse
    {
        Gate::authorize('view', $team);

        $period = $request->query('period', 'all');

        $tasksQuery = Task::where('team_id', $team->id);

        if ($period === 'weekly') {
            $tasksQuery->whereDate('created_at', '>=', now()->subWeek()->toDateString());
        } elseif ($period === 'monthly') {
            $tasksQuery->whereDate('created_at', '>=', now()->subMonth()->toDateString());
        } elseif ($period === 'yearly') {
            $tasksQuery->whereDate('created_at', '>=', now()->subYear()->toDateString());
        }

        $total = (clone $tasksQuery)->count();
        $todo = (clone $tasksQuery)->where('status', 'todo')->count();
        $onProgress = (clone $tasksQuery)->where('status', 'on_progress')->count();
        $done = (clone $tasksQuery)->where('status', 'done')->count();

        $membersCount = $team->members()->count();
        $members = $team->members()->with('user')->get();
        $memberTaskCounts = $members->map(fn ($member) => [
            'user_id' => $member->user_id,
            'name' => $member->user->name,
            'tasks_count' => Task::where('team_id', $team->id)
                ->where('user_id', $member->user_id)
                ->count(),
        ]);

        $completionRate = $total > 0 ? round(($done / $total) * 100, 1) : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'team_name' => $team->name,
                'period' => $period,
                'total' => $total,
                'by_status' => [
                    'todo' => $todo,
                    'on_progress' => $onProgress,
                    'done' => $done,
                ],
                'completion_rate' => $completionRate,
                'members_count' => $membersCount,
                'member_task_counts' => $memberTaskCounts,
            ],
        ]);
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user();

        $tasks = Task::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('team.members', fn ($mq) => $mq->where('user_id', $user->id));
        })->with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="tasks-export-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($tasks) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Judul', 'Status', 'Prioritas', 'Kategori',
                'Tenggat', 'Dibuat', 'Selesai',
            ]);

            foreach ($tasks as $task) {
                fputcsv($file, [
                    $task->id,
                    $task->title,
                    $task->status,
                    $task->priority,
                    $task->category?->name ?? '',
                    $task->deadline?->format('Y-m-d') ?? '',
                    $task->created_at->format('Y-m-d H:i'),
                    $task->status === 'done' ? ($task->updated_at->format('Y-m-d H:i') ?? '') : '',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
