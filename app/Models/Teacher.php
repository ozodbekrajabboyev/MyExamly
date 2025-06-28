<?php

namespace App\Models;

use App\Traits\ScopesSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms;

class Teacher extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory, ScopesSchool;

    public function exams():HasMany
    {
        return $this->hasMany(Exam::class);
    }
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject', 'teacher_id', 'subject_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function maktab(): BelongsTo
    {
        return $this->belongsTo(Maktab::class);
    }

    public static function getForm(): array
    {
        return [
            Section::make("Yangi o'qituvchi qo'shish")
                ->collapsible()
                ->columns(1)
                ->description("Yangi o'qituvchi qo'shish uchun quyidagilarni to'ldiring")
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Hidden::make('maktab_id')
                        ->default(fn () => auth()->user()->maktab_id)
                        ->required(),
                    TextInput::make('full_name')
                        ->label("O'qituvchining to'liq IFSH")
                        ->helperText("O'qituvchining to'liq Ism, Familiya va Sharifini kiriting.")
                        ->columnSpanFull()
                        ->required(),
                    TextInput::make('email')
                        ->label("O'qituvchining emailini kiriting")
                        ->required()
                        ->placeholder("example@example.com")
                        ->columnSpanFull(),
                    Select::make('subjects')
                        ->label("Fanni tanlang")
                        ->helperText("O'qituvchining fan(lar)ini tanlang.")
                        ->hint("Bir nechta fanni tanlashingiz mumkin")
                        ->columnSpanFull()
                        ->required()
                        ->relationship('subjects', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    TextInput::make('phone')
                        ->label("Telefon raqam")
                        ->helperText("O'qituvchining telefon raqamini kiriting.")
                        ->columnSpanFull()
                        ->prefix('+998')
                        ->tel(),
                ]),
        ];
    }
}
