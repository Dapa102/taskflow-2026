<?php

namespace App\Filament\Admin\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Lampiran File';

    protected static ?string $recordTitleAttribute = 'filename';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->required()
                    ->disk('public')
                    ->directory(fn () => 'attachments/' . $this->getOwnerRecord()->id)
                    ->maxSize(5120)
                    ->acceptedFileTypes([
                        'image/jpeg', 'image/png',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->storeFileNameUsing(fn ($file) => $file->getClientOriginalName()),

                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => auth()->id()),

                Forms\Components\Hidden::make('filename')
                    ->default(fn (Forms\Get $get) => ''),

                Forms\Components\Hidden::make('file_size')
                    ->default(0),

                Forms\Components\Hidden::make('mime_type')
                    ->default(''),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('filename')
            ->columns([
                Tables\Columns\TextColumn::make('filename')
                    ->label('Nama File')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper(explode('/', $state)[1] ?? $state)),

                Tables\Columns\TextColumn::make('file_size')
                    ->label('Ukuran')
                    ->formatStateUsing(fn (int $state): string => $state >= 1048576
                        ? round($state / 1048576, 1) . ' MB'
                        : round($state / 1024, 1) . ' KB'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Uploader'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diupload')
                    ->since(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Upload File')
                    ->mutateFormDataUsing(function (array $data): array {
                        $file = request()->file('data.file_path');
                        if ($file) {
                            $data['filename'] = $file->getClientOriginalName();
                            $data['file_size'] = $file->getSize();
                            $data['mime_type'] = $file->getMimeType();
                            $data['user_id'] = auth()->id();
                        }

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->after(fn ($record) => \Illuminate\Support\Facades\Storage::disk('public')->delete($record->file_path)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
