<?php
namespace App\Filament\Pages;


use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public static function canAccess(): bool
    {
        return auth()->user()->role_id === 'admin' || auth()->user()->role_id === 'teacher';
    }


}
