<?php

namespace App\Models;

use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    public function sinf(): BelongsTo
    {
        return $this->belongsTo(Sinf::class);
    }

    public static function getForm():array
    {
        return [
            Section::make("Yangi o'quvchi qo'shish")
                ->columns(1)
                ->description("Iltimos yangi o'quvchi qo'shish uchun quyidagilarni to'ldiring")
                ->icon('heroicon-o-information-circle')
                ->schema([
                    TextInput::make('full_name')
                        ->label("O'quvchining to'liq IFSH")
                        ->helperText("O'quvchining to'liq Ism, Familiya va Sharifini kiriting.")
                        ->required(),
                    Select::make('sinf_id')
                        ->label("Sinfni tanlang")
                        ->helperText("O'quvchining sinfini tanlang")
                        ->placeholder("Sinfni tanlang")
                        ->relationship('sinf', 'name')
                        ->createOptionForm(Sinf::getForm())
                        ->required(),
                ]),
        ];
    }
}
