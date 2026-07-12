<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExportController extends Controller
{
    private function registerInterFont(\Dompdf\Dompdf $dompdf): void
    {
        $fontDir = storage_path('fonts');
        $regular = $fontDir . '/Inter-Regular.ttf';
        $bold = $fontDir . '/Inter-Bold.ttf';
        $metrics = $dompdf->getFontMetrics();

        if (isset($metrics->getFontFamilies()['inter'])) {
            return;
        }

        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }

        if (file_exists($regular)) {
            try {
                $metrics->registerFont([
                    'family' => 'Inter',
                    'style' => 'normal',
                    'weight' => 'normal',
                ], $regular);
            } catch (\Exception $e) {
                logger()->warning('Inter font registration failed (regular): ' . $e->getMessage());
            }
        }
        if (file_exists($bold)) {
            try {
                $metrics->registerFont([
                    'family' => 'Inter',
                    'style' => 'normal',
                    'weight' => 'bold',
                ], $bold);
            } catch (\Exception $e) {
                logger()->warning('Inter font registration failed (bold): ' . $e->getMessage());
            }
        }
    }

    public function pmPerformance(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $cacheKey = 'export.pm-performance';
        $pms = Cache::remember($cacheKey, 300, function () {
            $pms = User::where('role', 'pm')
                ->where('is_active', true)
                ->get(['id', 'name', 'email']);

            $now = now();
            $stats = Task::selectRaw("
                    assigned_pm_id,
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as done_tasks,
                    SUM(CASE WHEN deadline IS NOT NULL AND deadline < ? AND status != ? THEN 1 ELSE 0 END) as overdue_tasks
                ", [TaskStatus::DONE, $now, TaskStatus::DONE])
                ->whereNotNull('assigned_pm_id')
                ->groupBy('assigned_pm_id')
                ->get()
                ->keyBy('assigned_pm_id');

            return $pms->map(function ($pm) use ($stats) {
                $s = $stats[$pm->id] ?? null;
                $total = $s ? (int) $s->total_tasks : 0;
                $done = $s ? (int) $s->done_tasks : 0;
                $overdue = $s ? (int) $s->overdue_tasks : 0;
                $pm->total_tasks = $total;
                $pm->done_tasks = $done;
                $pm->overdue_tasks = $overdue;
                $pm->on_time_rate = $total > 0 ? round(($done / $total) * 100, 2) : 0;
                return $pm;
            });
        });

        $pdf = Pdf::loadView('pdf.pm-performance', ['pms' => $pms]);
        $this->registerInterFont($pdf->getDomPDF());
        return $pdf->download('pm-performance.pdf');
    }

    public function memberPerformance(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $cacheKey = 'export.member-performance';
        $members = Cache::remember($cacheKey, 300, function () {
            $members = User::where('role', 'member')
                ->where('is_active', true)
                ->get(['id', 'name', 'email']);

            $stats = Task::selectRaw("
                    assigned_member_id,
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as done_tasks,
                    SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as review_tasks
                ", [TaskStatus::DONE, TaskStatus::IN_PROGRESS, TaskStatus::REVIEW])
                ->whereNotNull('assigned_member_id')
                ->groupBy('assigned_member_id')
                ->get()
                ->keyBy('assigned_member_id');

            $overdueStats = Task::whereNotNull('assigned_member_id')
                ->whereNotNull('deadline')
                ->where('deadline', '<', now())
                ->where('status', '!=', TaskStatus::DONE)
                ->selectRaw('assigned_member_id, COUNT(*) as cnt')
                ->groupBy('assigned_member_id')
                ->get()
                ->pluck('cnt', 'assigned_member_id');

            return $members->map(function ($member) use ($stats, $overdueStats) {
                $s = $stats[$member->id] ?? null;
                $total = $s ? (int) $s->total_tasks : 0;
                $done = $s ? (int) $s->done_tasks : 0;
                $inProgress = $s ? (int) $s->in_progress : 0;
                $review = $s ? (int) $s->review_tasks : 0;
                $overdue = (int) ($overdueStats[$member->id] ?? 0);
                $member->total_tasks = $total;
                $member->done_tasks = $done;
                $member->in_progress = $inProgress;
                $member->review_tasks = $review;
                $member->overdue_tasks = $overdue;
                $member->completion_rate = $total > 0 ? round(($done / $total) * 100, 2) : 0;
                return $member;
            });
        });

        $pdf = Pdf::loadView('pdf.member-performance', ['members' => $members]);
        $this->registerInterFont($pdf->getDomPDF());
        return $pdf->download('member-performance.pdf');
    }

    public function lateTasks(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $cacheKey = 'export.late-tasks';
        $tasks = Cache::remember($cacheKey, 300, function () {
            return Task::whereNotIn('status', [TaskStatus::DONE, TaskStatus::CANCELLED])
                ->whereNotNull('deadline')
                ->where('deadline', '<', now())
                ->with(['workspace:id,name', 'assignedPm:id,name', 'assignedMember:id,name'])
                ->select(['id', 'title', 'status', 'deadline', 'workspace_id', 'assigned_pm_id', 'assigned_member_id'])
                ->get();
        });

        $pdf = Pdf::loadView('pdf.late-tasks', ['tasks' => $tasks]);
        $this->registerInterFont($pdf->getDomPDF());
        return $pdf->download('late-tasks.pdf');
    }
}
