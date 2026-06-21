<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class PmPerformance extends Component
{
    public function render()
    {
        // Cache for 5 minutes as requested in PRD
        $pms = Cache::remember('admin_pm_performance', 300, function () {
            $pmsList = User::where('role', 'pm')->with('workspace')->get();
            
            foreach ($pmsList as $pm) {
                if (!$pm->workspace) {
                    $pm->total_tasks = 0;
                    $pm->done_tasks = 0;
                    $pm->overdue_tasks = 0;
                    $pm->on_time_rate = 0;
                    continue;
                }

                $tasks = Task::where('workspace_id', $pm->workspace->id)->get();
                
                $pm->total_tasks = $tasks->count();
                $pm->done_tasks = $tasks->where('status', 'done')->count();
                $pm->overdue_tasks = $tasks->filter(function($t) {
                    return $t->deadline && $t->deadline < now() && $t->status != 'done';
                })->count();
                
                $pm->on_time_rate = $pm->total_tasks > 0 
                    ? round(($pm->done_tasks / $pm->total_tasks) * 100, 2) 
                    : 0;
            }
            
            return $pmsList;
        });

        return view('livewire.admin.pm-performance', [
            'pms' => $pms
        ]);
    }
}
