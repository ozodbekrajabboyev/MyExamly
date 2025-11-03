<?php

namespace App\Models;

use App\Traits\ScopesSchool;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Forms;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory, ScopesSchool;

    protected $fillable = [
        'maktab_id',
        'sinf_id',
        'full_name',
    ];

    public function sinf(): BelongsTo
    {
        return $this->belongsTo(Sinf::class);
    }

    public function maktab(): BelongsTo
    {
        return $this->belongsTo(Maktab::class);
    }

    public function extractFirstAndLastName()
    {
        $cleaned = trim(preg_replace('/\s+/', ' ', $this->full_name));
        $parts = explode(' ', $cleaned);
        return [
            'first' => $parts[0] ?? '',
            'last' => $parts[1] ?? ''
        ];
    }

    public static function getForm():array
    {
        return [
            Section::make("Yangi o'quvchi qo'shish")
                ->columns(1)
                ->description("Iltimos yangi o'quvchi qo'shish uchun quyidagilarni to'ldiring")
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Hidden::make('maktab_id')
                        ->default(fn () => auth()->user()?->maktab_id)
                        ->required(),
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
