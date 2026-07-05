<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Task;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TaskSummaryWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        $tasks = $user->role === 'super_admin' ? Task::query() : Task::where('created_by', $user->id);

        $todo = (clone $tasks)->where('status', 'todo')->count();
        $onProgress = (clone $tasks)->where('status', 'on_progress')->count();
        $done = (clone $tasks)->where('status', 'done')->count();
        $total = $todo + $onProgress + $done;

        return [
            Stat::make('To-Do', $todo)
                ->description('Tugas yang belum dimulai')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('info'),
            Stat::make('On Progress', $onProgress)
                ->description('Tugas yang sedang dikerjakan')
                ->icon('heroicon-o-arrow-path')
                ->color('warning'),
            Stat::make('Done', $done)
                ->description('Tugas yang sudah selesai')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Total', $total)
                ->description('Seluruh tugas')
                ->icon('heroicon-o-document-text')
                ->color('gray'),
        ];
    }
}
