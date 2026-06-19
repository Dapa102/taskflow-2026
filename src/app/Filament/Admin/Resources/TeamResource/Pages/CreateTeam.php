<?php

namespace App\Filament\Admin\Resources\TeamResource\Pages;

use App\Filament\Admin\Resources\TeamResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_id'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->members()->create([
            'user_id' => auth()->id(),
            'role' => 'admin',
            'joined_at' => now(),
        ]);
    }
}
