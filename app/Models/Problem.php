<?php

namespace App\Models;

use App\Traits\ScopesSchool;
use Closure;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
class Problem extends Model
{
    /** @use HasFactory<\Database\Factories\ProblemFactory> */
    use HasFactory, ScopesSchool;

    public function exam():BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function maktab(): BelongsTo
    {
        return $this->belongsTo(Maktab::class);
    }
    public function marks():hasMany
    {
        return $this->hasMany(Mark::class);
    }

    protected static function calculateNextProblemNumber($examId): int
    {
        if (!$examId) return 1;

        return Problem::where('exam_id', $examId)
            ->orderBy('problem_number', 'desc')
            ->first()?->problem_number + 1 ?? 1;
    }

    public static function getForm()
    {
        return [
            Select::make('exam_id')
                ->afterStateUpdated(function ($state, Set $set) {
                    $set('problem_number', Problem::calculateNextProblemNumber($state));
                })
                ->columnSpanFull()
                ->label('Imtihon nomi')
                ->relationship('exam', 'id')
                ->options(function () {
                    $user = auth()->user();

                    return \App\Models\Exam::whereHas('sinf', function ($query) use ($user) {
                        $query->where('maktab_id', $user->maktab_id);
                    })
                        ->when($user->role->name === 'teacher', function ($query) use ($user) {
                            $query->where('teacher_id', $user->teacher->id); // ✅ Only show exams owned by this teacher
                        })
                        ->with(['sinf', 'subject', 'problems'])
                        ->get()
                        ->mapWithKeys(function ($exam) {
                            $label = "{$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}";

                            if ($exam->problems->count() >= $exam->problems_count) {
                                $label .= ' (Yetarlicha topshiriq mavjud)';
                            }

                            return [$exam->id => $label];
                        });
                })

                ->disableOptionWhen(function ($value) {
                    $exam = Exam::find($value);
                    return $exam && $exam->problems->count() >= $exam->problems_count;
                })
                ->preload()
                ->live()
                ->required(),

            TextInput::make('problem_number')
                ->label('Topshiriq raqami')
                ->disabled()
                ->dehydrated()
                ->default(function (Get $get) {
                    $examId = $get('exam_id');

                    if (!$examId) return 1;

                    $lastProblem = Problem::where('exam_id', $examId)->orderBy('problem_number', 'desc')->first()->problem_number;

                    return $lastProblem;
                })
                ->hidden(fn (Get $get): bool => !$get('exam_id')), // Only show when exam is selected,

            TextInput::make('max_mark')
                ->label('Maximum ball')
                ->required()
                ->numeric()
                ->minValue(0)
                ->hidden(fn (Get $get): bool => !$get('exam_id')), // Only show when exam is selected
            Forms\Components\Hidden::make('maktab_id')
                ->default(fn () => auth()->user()->maktab_id)
                ->required(),
        ];
    }
}
