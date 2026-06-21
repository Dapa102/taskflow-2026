<?php

namespace App\Livewire\Atasan;

use Livewire\Component;
use App\Models\Task;
use Livewire\Attributes\Layout;

#[Layout('layouts.atasan')]
class AtasanDashboard extends Component
{
    public function render()
    {
        $userId = auth()->id();

        $total = Task::where('created_by', $userId)->count();
        $given = Task::where('created_by', $userId)->whereNotNull('assigned_to')->count();
        $pending = Task::where('created_by', $userId)->whereNull('assigned_to')->count();
        $done = Task::where('created_by', $userId)->where('status', 'done')->count();

        return view('livewire.atasan.atasan-dashboard', [
            'total' => $total,
            'given' => $given,
            'pending' => $pending,
            'done' => $done,
        ]);
    }
}
