<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Task;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class TaskTrendChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 3;

    protected static ?string $heading = 'Tren Tugas per Minggu';

    protected static string $color = 'primary';

    protected function getData(): array
    {
        $userId = auth()->id();
        $labels = [];
        $created = [];
        $completed = [];

        for ($i = 6; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();

            $labels[] = $weekStart->format('d M') . ' - ' . $weekEnd->format('d M');

            $created[] = Task::where('user_id', $userId)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->count();

            $completed[] = Task::where('user_id', $userId)
                ->where('status', 'done')
                ->whereBetween('updated_at', [$weekStart, $weekEnd])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Dibuat',
                    'data' => $created,
                    'backgroundColor' => '#0ea5e9',
                    'borderColor' => '#0284c7',
                ],
                [
                    'label' => 'Selesai',
                    'data' => $completed,
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#16a34a',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
