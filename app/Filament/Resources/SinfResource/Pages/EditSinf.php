<?php

namespace App\Filament\Resources\SinfResource\Pages;

use App\Filament\Resources\SinfResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSinf extends EditRecord
{
    protected static string $resource = SinfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
