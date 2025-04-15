<?php

namespace App\Models;

use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mark extends Model
{
    /** @use HasFactory<\Database\Factories\MarkFactory> */
    use HasFactory;

    public function exam():BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function sinf():BelongsTo
    {
        return $this->belongsTo(Sinf::class);
    }

    public function student():BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }

    public static function getForm()
    {
        return [
            Section::make('Yangi imtihon yaratish')
                ->collapsible()
                ->columns(2)
                ->description("Yangi imtihon yaratish uchun quyidagilarni to'ldiring!")
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Select::make('exam_id')
                        ->label('Imtihon nomi')
                        ->options(function () {
                            return Exam::with(['subject'])
                                ->get()
                                ->mapWithKeys(function ($exam) {
                                    $label = $exam->subject
                                        ? "{$exam->subject->name} - {$exam->type} #{$exam->serial_number}"
                                        : "Noma'lum - {$exam->type} #{$exam->serial_number}";

                                    return [$exam->id => $label];
                                });
                        })
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                    Forms\Components\Select::make('sinf_id')
                        ->label('Sinf')
                        ->options(Sinf::all()->pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('student_id', null)),

                    Forms\Components\Select::make('student_id')
                        ->label('O\'quvchi')
                        ->options(function (callable $get) {
                            $sinfId = $get('sinf_id');
                            if (!$sinfId) {
                                return [];
                            }
                            return Student::where('sinf_id', $sinfId)
                                ->get()
                                ->pluck('full_name', 'id');
                        })
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('problem_id')
                        ->label('Masala')
                        ->options(function (callable $get) {
                            $examId = $get('exam_id');
                            if (!$examId) {
                                return [];
                            }
                            return Problem::where('exam_id', $examId)
                                ->get()
                                ->pluck('problem_number', 'id');
                        })
                        ->required(),

                    Forms\Components\TextInput::make('mark')
                        ->label('Baho')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100),
                ])
        ];
    }
}

