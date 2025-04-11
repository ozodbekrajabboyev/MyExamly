<?php

namespace App\Models;

use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject', 'subject_id', 'teacher_id');
    }

    public static function getForm(): array
    {
        return [
            Section::make("Yangi fan qo'shish")
                ->columns(1)
                    ->description("Yangi fan qo'shish uchun quyidagilarni to'ldiring")
                ->icon('heroicon-o-information-circle')
                ->schema([
                    TextInput::make('name')
                        ->label('Fan nomi')
                        ->helperText("Fan nomini kiriting")
                        ->required(),
                ]),
        ];
    }
}
