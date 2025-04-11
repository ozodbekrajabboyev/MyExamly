<?php

namespace App\Models;

use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sinf extends Model
{
    /** @use HasFactory<\Database\Factories\SinfFactory> */
    use HasFactory;

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public static function getForm():array
    {
        return [
            Section::make("Yangi sinf qo'shish")
                ->columns(1)
                ->schema([
                    TextInput::make('name')
                        ->label('Sinf nomi')
                        ->helperText('Sinf nomini kiriting')
                        ->required()
                        ->maxLength(255),
                ]),
        ];
    }
}
