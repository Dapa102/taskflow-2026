<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TaskResource\Pages;
use App\Filament\Admin\Resources\TaskResource\RelationManagers;
use App\Models\Category;
use App\Models\Task;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isDiscovered = false;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Task Management';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return static::getModel()::count();
        }
        return static::getModel()::where('created_by', $user->id)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Tugas')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpan('full'),

                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->options(fn (): array => auth()->user()->role === 'admin'
                                ? Category::pluck('name', 'id')->toArray()
                                : Category::where('user_id', auth()->id())->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('team_id')
                            ->label('Tim')
                            ->options(Team::pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->options([
                                'todo' => 'To-Do',
                                'on_progress' => 'On Progress',
                                'done' => 'Done',
                            ])
                            ->default('todo')
                            ->required(),

                        Forms\Components\Select::make('priority')
                            ->label('Prioritas')
                            ->options([
                                'low' => 'Rendah',
                                'medium' => 'Sedang',
                                'high' => 'Tinggi',
                            ])
                            ->default('medium')
                            ->required(),

                        Forms\Components\DatePicker::make('deadline')
                            ->label('Tenggat Waktu')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->format('Y-m-d')
                            ->closeOnDateSelection()
                            ->minDate(now()->subYear()->startOfDay()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => auth()->user()->role === 'admin'
                ? $query->with(['category', 'team', 'assignees', 'comments.user', 'subtasks', 'attachments'])
                : $query->where('created_by', auth()->id())->with(['category', 'team', 'assignees', 'comments.user', 'subtasks', 'attachments']))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn (Task $record): string => $record->title),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('team.name')
                    ->label('Tim')
                    ->badge()
                    ->color('primary')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'todo' => 'info',
                        'on_progress' => 'warning',
                        'done' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'todo' => 'To-Do',
                        'on_progress' => 'On Progress',
                        'done' => 'Done',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'warning',
                        'high' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Rendah',
                        'medium' => 'Sedang',
                        'high' => 'Tinggi',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('deadline')
                    ->label('Tenggat')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn (Task $record): string => $record->isOverdue() ? 'danger' : 'gray')
                    ->icon(fn (Task $record): ?string => $record->isOverdue() ? 'heroicon-o-exclamation-triangle' : null),

                Tables\Columns\TextColumn::make('subtasks_progress')
                    ->label('Progres')
                    ->state(fn (Task $record): string => $record->subtasks()->count() > 0
                        ? $record->subtasks()->where('is_completed', true)->count() . '/' . $record->subtasks()->count()
                        : '—')
                    ->icon('heroicon-o-check-badge'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('deadline', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'todo' => 'To-Do',
                        'on_progress' => 'On Progress',
                        'done' => 'Done',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioritas')
                    ->options([
                        'low' => 'Rendah',
                        'medium' => 'Sedang',
                        'high' => 'Tinggi',
                    ]),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(fn (): array => auth()->user()->role === 'admin'
                        ? Category::pluck('name', 'id')->toArray()
                        : Category::where('user_id', auth()->id())->pluck('name', 'id')->toArray()),
            ])
            ->actions([
                Tables\Actions\Action::make('task_detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (Task $record) => $record->title)
                    ->modalWidth('2xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Task $record) => view('filament.admin.resources.task-resource.detail', ['task' => $record])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubtasksRelationManager::class,
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\AttachmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
