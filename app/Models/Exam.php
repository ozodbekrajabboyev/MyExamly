<?php

namespace App\Models;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms\Get;  // $get uchun
use Filament\Forms\Set;  // $set uchun
use Illuminate\Database\Eloquent\Builder; // Builder uchun
use Filament\Forms; // Forms namespace uchun (agar kerak bo'lsa)
class Exam extends Model
{
    /** @use HasFactory<\Database\Factories\ExamFactory> */
    use HasFactory;

    public function sinf():BelongsTo
    {
        return $this->belongsTo(Sinf::class);
    }

    public function subject():BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher():BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function metod():BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'metod_id');
    }
    public function problems(): HasMany
    {
        return $this->hasMany(Problem::class);
    }

    public static function getForm1():array
    {
        return [
            Section::make('Yangi imtihon yaratish')
                ->collapsible()
                ->columns(2)
                ->description("Yangi imtihon yaratish uchun quyidagilarni to'ldiring!")
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Select::make('sinf_id')
                        ->label('Sinfni tanlang')
                        ->relationship('sinf', 'name')
                        ->required(),
                    Select::make('subject_id')
                        ->label('Fanni tanlang')
                        ->relationship('subject', 'name')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            $set('teacher_id', null); // Fan o'zgarganda o'qituvchini nolga tenglashtirish
                        }),
                    Select::make('teacher_id')
                        ->label("Fan o'qituvchisini tanlang")
                        ->options(function (Forms\Get $get) {
                            $subjectId = $get('subject_id');

                            if (!$subjectId) {
                                return [];
                            }

                            return \App\Models\Teacher::whereHas('subjects', function (Builder $query) use ($subjectId) {
                                $query->where('subjects.id', $subjectId);
                            })
                                ->pluck('full_name', 'id');
                        })
                        ->searchable()
                        ->required(),
//                        ->disabled(fn (Forms\Get $get) => !$get('subject_id')),
                    Select::make('type')
                        ->label('Imtihon turini tanlang')
                        ->options(['BSB', 'CHSB'])
                        ->required(),
                    TextInput::make('serial_number')
                        ->label('Imtihon tartib raqamini kiriting')
                        ->columnSpanFull()
                        ->hint("Masalan, 5-BSB, 2-CHSB dagi tartib raqamini kiriting")
                        ->hintIcon('heroicon-o-information-circle')
                        ->required()
                        ->numeric(),
                    TextInput::make('problems_count')
                        ->label('Topshiriqlar sonini kiriting')
                        ->required()
                        ->numeric(),
                    Select::make('metod_id')
                        ->label('Metodbirlashma rahbarini tanlang')
                        ->relationship('metod', 'full_name')
                        ->required(),
                ]),

        ];
    }

    public static function getForm(): array
    {
        return [
            Section::make('Yangi imtihon yaratish')
                ->collapsible()
                ->columns(2)
                ->description("Yangi imtihon yaratish uchun quyidagilarni to'ldiring!")
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Select::make('sinf_id')
                        ->label('Sinfni tanlang')
                        ->relationship('sinf', 'name')
                        ->required(),

                    Select::make('subject_id')
                        ->label('Fanni tanlang')
                        ->relationship('subject', 'name')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            $set('teacher_id', null); // Fan o'zgarganda o'qituvchini nolga tenglashtirish
                        }),

                    Select::make('teacher_id')
                        ->label("Fan o'qituvchisini tanlang")
                        ->options(function (Get $get) {
                            $subjectId = $get('subject_id');

                            if (!$subjectId) {
                                return [];
                            }

                            return \App\Models\Teacher::whereHas('subjects', function (Builder $query) use ($subjectId) {
                                $query->where('subjects.id', $subjectId);
                            })
                                ->pluck('full_name', 'id');
                        })
                        ->searchable()
                        ->required()
                        ->disabled(fn (Get $get): bool => !$get('subject_id')),

                    Select::make('type')
                        ->label('Imtihon turini tanlang')
                        ->options([
                            'BSB' => 'BSB',
                            'CHSB' => 'CHSB'
                        ])
                        ->required(),

                    TextInput::make('serial_number')
                        ->label('Imtihon tartib raqamini kiriting')
                        ->columnSpanFull()
                        ->hint("Masalan, 5-BSB, 2-CHSB dagi tartib raqamini kiriting")
                        ->hintIcon('heroicon-o-information-circle')
                        ->required()
                        ->numeric(),

                    TextInput::make('problems_count')
                        ->label('Topshiriqlar sonini kiriting')
                        ->required()
                        ->numeric(),

                    Select::make('metod_id')
                        ->label('Metodbirlashma rahbarini tanlang')
                        ->relationship('metod', 'full_name')
                        ->required(),
                ]),
        ];
    }
}
