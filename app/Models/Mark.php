<?php

namespace App\Models;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;

class Mark extends Model
{
    /** @use HasFactory<\Database\Factories\MarkFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'problem_id',
        'exam_id',
        'sinf_id',
        'mark',
    ];

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

    public static function getForm(){
        return [
            Section::make("O'quvchilarni baholarini kiriting")
                ->collapsible()
                ->description("O'quvchilarning baholarini kiritish uchun quyidagilarni to'ldiring")
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Select::make('exam_id')
                        ->label('Imtihon tanlang')
                        ->options(Exam::with(['sinf', 'subject'])->get()->mapWithKeys(fn ($exam) => [
                            $exam->id => "{$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}"
                        ]))
                        ->live()
                        ->disabled(fn(string $operation): bool => $operation === 'edit')
                        ->required()
                        ->columnSpanFull(),

                    Grid::make()
                        ->schema(function (Get $get) {
                            $examId = $get('exam_id');

                            if (!$examId) return [];

                            $exam = Exam::with(['sinf.students', 'problems' => fn($q) => $q->orderBy('problem_number')])->find($examId);
                            if (!$exam) return [];

                            $students = $exam->sinf->students->sortBy('full_name');
                            $problems = $exam->problems;

                            $schema = [];
                            $headerC = new HtmlString("<span class='text-green-500 font-bold text-l'>O'quvchi / Topshiriq</span>");
                            $header = [
                                Placeholder::make('')->content(fn () => $headerC),
                            ];

                            foreach ($problems as $problem) {
                                $header[] = Placeholder::make('')
                                    ->content(new HtmlString("<span class='text-green-500 font-bold text-l'>{$problem->problem_number}-topshiriq (Max: {$problem->max_mark})</span>"));
                            }

                            $schema[] = Grid::make(count($header))->schema($header);

                            // Students + Inputs
                            foreach ($students as $student) {
                                $row = [
                                    Placeholder::make('')
                                        ->content($student->full_name),
                                ];

                                foreach ($problems as $problem) {
                                    $existingMark = Mark::where('student_id', $student->id)
                                        ->where('problem_id', $problem->id)
                                        ->first();

                                    $row[] = TextInput::make("marks.{$student->id}_{$problem->id}")
                                        ->hiddenLabel()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue($problem->max_score)
                                        ->default(1);
                                }

                                $schema[] = Grid::make(count($row))->schema($row);
                            }

                            return $schema;
                        })
                        ->extraAttributes(['class' => 'mark-table'])
                ]),
        ];
    }
}

