<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

#[Layout('layouts.super-admin')]
class PmPerformance extends Component
{
    public function render()
    {
        $pms = Cache::remember('admin_pm_performance', 300, function () {
            $pmsList = User::where('role', 'pm')->with('workspace')->get();

            foreach ($pmsList as $pm) {
                $tasks = Task::where('assigned_pm_id', $pm->id)->get();

                $pm->total_tasks = $tasks->count();
                $pm->done_tasks = $tasks->where('status', 'done')->count();
                $pm->overdue_tasks = $tasks->filter(fn($t) =>
                    $t->deadline && $t->deadline < now() && $t->status !== 'done'
                )->count();
                $pm->on_time_rate = $pm->total_tasks > 0
                    ? round(($pm->done_tasks / $pm->total_tasks) * 100, 2)
                    : 0;
            }

            return $pmsList;
        });

        return view('livewire.super-admin.pm-performance', [
            'pms' => $pms
        ]);
    }
}
