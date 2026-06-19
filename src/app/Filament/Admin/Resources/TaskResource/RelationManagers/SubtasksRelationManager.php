<?php

namespace App\Filament\Admin\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubtasksRelationManager extends RelationManager
{
    protected static string $relationship = 'subtasks';

    protected static ?string $title = 'Sub-tugas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul Sub-tugas')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Toggle::make('is_completed')
                    ->label('Selesai')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\IconColumn::make('is_completed')
                    ->label('✓')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->formatStateUsing(fn (string $state, $record): string => $record->is_completed
                        ? '<span style="text-decoration: line-through; opacity: 0.5">' . e($state) . '</span>'
                        : e($state))
                    ->html(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle')
                    ->label(fn ($record): string => $record->is_completed ? 'Batal' : 'Selesai')
                    ->icon(fn ($record): string => $record->is_completed ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-check')
                    ->color(fn ($record): string => $record->is_completed ? 'gray' : 'success')
                    ->action(fn ($record) => $record->update(['is_completed' => !$record->is_completed])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
