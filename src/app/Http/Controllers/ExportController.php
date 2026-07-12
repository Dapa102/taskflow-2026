<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function pmPerformance(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $pms = User::where('role', 'pm')
            ->with('workspaces')
            ->where('is_active', true)
            ->get()
            ->map(function ($pm) {
                $tasks = Task::where('assigned_pm_id', $pm->id)->get();
                $pm->total_tasks = $tasks->count();
                $pm->done_tasks = $tasks->where('status', TaskStatus::DONE)->count();
                $pm->overdue_tasks = $tasks->filter(fn($t) =>
                    $t->deadline && $t->deadline < now() && $t->status !== TaskStatus::DONE
                )->count();
                $pm->on_time_rate = $pm->total_tasks > 0
                    ? round(($pm->done_tasks / $pm->total_tasks) * 100, 2)
                    : 0;
                return $pm;
            });

        $pdf = Pdf::loadView('pdf.pm-performance', ['pms' => $pms]);

        return $pdf->download('pm-performance.pdf');
    }

    public function memberPerformance(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $members = User::where('role', 'member')
            ->where('is_active', true)
            ->get()
            ->map(function ($member) {
                $tasks = Task::where('assigned_member_id', $member->id)->get();
                $member->total_tasks = $tasks->count();
                $member->done_tasks = $tasks->where('status', TaskStatus::DONE)->count();
                $member->overdue_tasks = $tasks->filter(fn($t) =>
                    $t->deadline && $t->deadline < now() && $t->status !== TaskStatus::DONE
                )->count();
                $member->on_time_rate = $member->total_tasks > 0
                    ? round(($member->done_tasks / $member->total_tasks) * 100, 2)
                    : 0;
                return $member;
            });

        $pdf = Pdf::loadView('pdf.member-performance', ['members' => $members]);

        return $pdf->download('member-performance.pdf');
    }

    public function lateTasks(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $tasks = Task::whereNotIn('status', [TaskStatus::DONE, TaskStatus::CANCELLED])
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->with(['workspace', 'assignedPm', 'assignedMember'])
            ->get();

        $pdf = Pdf::loadView('pdf.late-tasks', ['tasks' => $tasks]);

        return $pdf->download('late-tasks.pdf');
    }
}
