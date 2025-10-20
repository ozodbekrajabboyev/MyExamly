<?php

namespace App\Models;

use App\Traits\ScopesSchool;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms;

class Sinf extends Model
{
    /** @use HasFactory<\Database\Factories\SinfFactory> */
    use HasFactory, ScopesSchool;

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function maktab(): BelongsTo
    {
        return $this->belongsTo(Maktab::class);
    }

    public static function getForm():array
    {
        return [
            Section::make("Yangi sinf qo'shish")
                ->columns(1)
                ->schema([
                    Forms\Components\Hidden::make('maktab_id')
                        ->default(fn () => auth()->user()->maktab_id)
                        ->required(),
                    TextInput::make('name')
                        ->label('Sinf nomi')
                        ->helperText('Sinf nomini kiriting')
                        ->required()
                        ->maxLength(255),
                ]),
        ];
    }
}
