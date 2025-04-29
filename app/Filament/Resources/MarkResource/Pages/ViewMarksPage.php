<?php

namespace App\Filament\Resources\MarkResource\Pages;

use App\Filament\Resources\MarkResource;
use Filament\Resources\Pages\Page;

class ViewMarksPage extends Page
{
    protected static string $resource = MarkResource::class;

    protected static string $view = 'filament.resources.mark-resource.pages.view-marks-page';
}
