<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\TeamResource;
use App\Models\Team;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TeamTasksWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Team::query()->withCount('members')
            )
            ->heading('Tim & Tugas Sedang Dikerjakan')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tim')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('members_count')
                    ->label('Anggota')
                    ->counts('members')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasks')
                    ->label('Tugas On Progress')
                    ->html()
                    ->state(function (Team $team) {
                        $tasks = $team->tasks()
                            ->where('status', 'on_progress')
                            ->with('assignees')
                            ->limit(5)
                            ->get();

                        if ($tasks->isEmpty()) {
                            return '<span class="text-gray-400">—</span>';
                        }

                        return $tasks->map(fn ($task) =>
                            '<div class="flex items-center justify-between gap-2 py-1">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium truncate">'
                                        . e($task->title) .
                                    '</div>
                                    <div class="flex gap-2 text-xs text-gray-500">
                                        <span>' . ucfirst($task->priority) . '</span>
                                        <span>·</span>
                                        <span>' . ($task->deadline ? $task->deadline->format('d M') : 'No deadline') . '</span>
                                        ' . ($task->isOverdue() ? '<span class="text-danger-500 font-semibold">Overdue</span>' : '') . '
                                    </div>
                                </div>
                                <div class="flex -space-x-2">'
                                    . $task->assignees->map(fn ($u) =>
                                        '<div class="w-7 h-7 rounded-full bg-gray-700 flex items-center justify-center text-xs font-medium ring-2 ring-gray-800" title="' . e($u->name) . '">'
                                            . strtoupper(substr($u->name, 0, 2)) .
                                        '</div>'
                                    )->implode('') .
                                '</div>
                            </div>'
                        )->implode('');
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-arrow-right')
                    ->url(fn (Team $record) => TeamResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
