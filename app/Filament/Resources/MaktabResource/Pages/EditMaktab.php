<?php

namespace App\Filament\Resources\MaktabResource\Pages;

use App\Filament\Resources\MaktabResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaktab extends EditRecord
{
    protected static string $resource = MaktabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
