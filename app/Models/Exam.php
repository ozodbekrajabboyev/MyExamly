<?php

namespace App\Models;

use App\Traits\ScopesSchool;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms\Get;  // $get uchun
use Filament\Forms\Set;  // $set uchun
use Illuminate\Database\Eloquent\Builder; // Builder uchun
use Filament\Forms;
use Illuminate\Support\Facades\Auth;

// Forms namespace uchun (agar kerak bo'lsa)
class Exam extends Model
{
    /** @use HasFactory<\Database\Factories\ExamFactory> */
    use HasFactory, ScopesSchool;

    public function sinf():BelongsTo
    {
        return $this->belongsTo(Sinf::class);
    }

    public function maktab(): BelongsTo
    {
        return $this->belongsTo(Maktab::class);
    }

    public function marks():HasMany
    {
        return $this->hasMany(Mark::class);
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

    public static function getForm(): array
    {
        return [
            Section::make('Yangi imtihon yaratish')
                ->collapsible()
                ->columns(2)
                ->description("Yangi imtihon yaratish uchun quyidagilarni to'ldiring!")
                ->icon('heroicon-o-information-circle')
                ->schema([

                    Forms\Components\Hidden::make('maktab_id')
                        ->default(fn () => auth()->user()->maktab_id)
                        ->required(),
                    Select::make('sinf_id')
                        ->label('Sinfni tanlang')
                        ->relationship('sinf', 'name')
                        ->options(function () {
                            return \App\Models\Sinf::where('maktab_id', auth()->user()->maktab_id)
                                ->pluck('name', 'id');
                        })
                        ->required(),


                    Select::make('subject_id')
                        ->label('Fanni tanlang')
                        ->relationship(
                            name: 'subject',
                            titleAttribute: 'name',
                            modifyQueryUsing: function ($query) {
                                $user = Auth::user();

                                if ($user->role->name === 'teacher') {
                                    // Adjust this according to your subject-teacher relationship
                                    return $query->whereHas('teachers', function ($q) use ($user) {
                                        $q->where('teachers.id', $user->teacher->id);
                                    });
                                }

                                return $query;
                            }
                        )
                        ->required(),

                    Forms\Components\Hidden::make('teacher_id')
                        ->default(fn () => auth()->user()->teacher->id)
                        ->required(),

//                    Select::make('teacher_id')
//                        ->label("Fan o'qituvchisini tanlang")
//                        ->options(function (Get $get) {
//                            $subjectId = $get('subject_id');
//
//                            if (!$subjectId) {
//                                return [];
//                            }
//
//                            return \App\Models\Teacher::whereHas('subjects', function (Builder $query) use ($subjectId) {
//                                $query->where('subjects.id', $subjectId);
//                            })
//                                ->pluck('full_name', 'id');
//                        })
//                        ->searchable()
//                        ->required()
//                        ->disabled(fn (Get $get): bool => !$get('subject_id')),

                    Select::make('type')
                        ->label('Imtihon turini tanlang')
                        ->columnSpanFull()
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
                        ->options(function () {
                            return \App\Models\Teacher::where('maktab_id', auth()->user()->maktab_id)
                                ->pluck('full_name', 'id');
                        })
                        ->required(),
                    ToggleButtons::make('status')
                        ->label('Imtihon Holati')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->inline()
                        ->colors([
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                        ])
                        ->icons([
                            'pending' => 'heroicon-m-clock',
                            'approved' => 'heroicon-m-check-circle',
                            'rejected' => 'heroicon-m-x-circle',
                        ])
                        ->visible(fn () => Auth::user()?->role?->name === 'admin'),
                ]),
        ];
    }
}
