<?php

namespace App\Filament\Resources\MaktabResource\Pages;

use App\Filament\Resources\MaktabResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaktabs extends ListRecords
{
    protected static string $resource = MaktabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label("Yangi maktab yaratish"),
        ];
    }
}
