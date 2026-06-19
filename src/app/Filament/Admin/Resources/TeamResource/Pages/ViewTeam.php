<?php

namespace App\Filament\Admin\Resources\TeamResource\Pages;

use App\Filament\Admin\Resources\TeamResource;
use App\Models\Task;
use App\Models\Team;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewTeam extends ViewRecord
{
    protected static string $resource = TeamResource::class;

    protected function mutateRecord(\Illuminate\Database\Eloquent\Model $record): \Illuminate\Database\Eloquent\Model
    {
        $record->loadCount('members');
        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $team = $this->record;

        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Tim')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')->label('Nama Tim'),
                        Infolists\Components\TextEntry::make('invite_code')
                            ->label('Kode Undangan')
                            ->badge()
                            ->copyable()
                            ->color('gray'),
                        Infolists\Components\TextEntry::make('owner.name')->label('Pemilik'),
                        Infolists\Components\TextEntry::make('members_count')
                            ->label('Jumlah Anggota')
                            ->state(fn (Team $record) => $record->members()->count()),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y'),
                    ])->columns(3),

                Infolists\Components\Section::make('Anggota Tim')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('members')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name')
                                    ->label('Nama'),
                                Infolists\Components\TextEntry::make('role')
                                    ->label('Peran')
                                    ->badge()
                                    ->color(fn (string $state) => $state === 'admin' ? 'primary' : 'gray'),
                                Infolists\Components\TextEntry::make('joined_at')
                                    ->label('Bergabung')
                                    ->dateTime('d M Y'),
                            ])->columns(3),
                    ]),

                $this->taskSection($team, 'todo', 'To-Do', 'warning'),
                $this->taskSection($team, 'on_progress', 'On Progress', 'primary'),
                $this->taskSection($team, 'done', 'Selesai', 'success'),
            ]);
    }

    protected function taskSection(Team $team, string $status, string $label, string $color): Infolists\Components\Section
    {
        $tasks = $team->tasks()
            ->where('status', $status)
            ->with(['category', 'assignees', 'user'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Infolists\Components\Section::make("$label (" . $tasks->count() . ')')
            ->collapsible()
            ->collapsed($status !== 'on_progress')
            ->schema(
                $tasks->isEmpty()
                    ? [Infolists\Components\TextEntry::make('empty_' . $status)
                          ->label('')
                          ->default('Tidak ada tugas dengan status ini.')]
                    : $tasks->map(fn (Task $task) =>
                        Infolists\Components\TextEntry::make('t' . $task->id . '_' . $status)
                            ->label($task->title)
                            ->default(implode(' · ', array_filter([
                                ucfirst($task->priority),
                                $task->category?->name,
                                $task->deadline?->format('d M Y'),
                                $task->assignees->pluck('name')->implode(', ') ? 'Ditugaskan ke: ' . $task->assignees->pluck('name')->implode(', ') : null,
                            ])))
                    )->all()
            );
    }
}
