<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Task;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class TaskStatusChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected static ?string $heading = 'Distribusi Status Tugas';

    protected static string $color = 'primary';

    protected function getData(): array
    {
        $userId = auth()->id();

        $todo = Task::where('user_id', $userId)->where('status', 'todo')->count();
        $onProgress = Task::where('user_id', $userId)->where('status', 'on_progress')->count();
        $done = Task::where('user_id', $userId)->where('status', 'done')->count();

        return [
            'datasets' => [
                [
                    'data' => [$todo, $onProgress, $done],
                    'backgroundColor' => ['#0ea5e9', '#f59e0b', '#22c55e'],
                    'borderColor' => ['#0284c7', '#d97706', '#16a34a'],
                ],
            ],
            'labels' => ['To-Do', 'On Progress', 'Done'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
