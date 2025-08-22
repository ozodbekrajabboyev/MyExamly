<?php

namespace App\Models;

use App\Traits\ScopesSchool;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class Mark extends Model
{
    /** @use HasFactory<\Database\Factories\MarkFactory> */
    use HasFactory, ScopesSchool;

    protected $fillable = [
        'student_id',
        'exam_id',
        'sinf_id',
        'maktab_id',
        'problem_id', // This will store the problem ID from JSON
        'mark'
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function maktab(): BelongsTo
    {
        return $this->belongsTo(Maktab::class);
    }

    public function sinf(): BelongsTo
    {
        return $this->belongsTo(Sinf::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // Helper method to get problem details from exam's JSON
    public function getProblemAttribute()
    {
        if (!$this->exam || !$this->problem_id) {
            return null;
        }

        $problems = collect(is_string($this->exam->problems) ? json_decode($this->exam->problems, true) : $this->exam->problems);
        return $problems->firstWhere('id', $this->problem_id);
    }

    public static function getForm()
    {
        return [
            Section::make("O'quvchilarni baholarini kiriting")
                ->collapsible()
                ->description("O'quvchilarning baholarini kiritish uchun quyidagilarni to'ldiring")
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Hidden::make('maktab_id')
                        ->default(fn () => auth()->user()->maktab_id)
                        ->required(),

                    Forms\Components\Select::make('exam_id')
                        ->label('Imtihon tanlang')
                        ->options(function () {
                            $user = auth()->user();

                            $query = \App\Models\Exam::query()
                                ->where('maktab_id', $user->maktab_id)
                                ->whereNotNull('problems') // Only exams with problems
                                ->whereDoesntHave('marks')   // Only exams without marks
                                ->with(['sinf', 'subject']);

                            // If teacher should only see their own exams
                            if ($user->role->name === 'teacher') {
                                $query->where('teacher_id', $user->teacher->id);
                            }

                            return $query->get()
                                ->filter(function ($exam) {
                                    // Additional check to ensure problems JSON is valid and not empty
                                    $problems = is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems;
                                    return is_array($problems) && count($problems) > 0;
                                })
                                ->mapWithKeys(function ($exam) {
                                    $label = "{$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}";
                                    return [$exam->id => $label];
                                });
                        })
                        ->live()
                        ->disabled(fn(string $operation): bool => $operation === 'edit')
                        ->required()
                        ->columnSpanFull(),

                    Grid::make()
                        ->schema(function (Get $get) {
                            $examId = $get('exam_id');

                            if (!$examId) return [];

                            $exam = Exam::with(['sinf.students'])->find($examId);
                            if (!$exam || !$exam->problems) return [];

                            // Parse problems from JSON or array
                            $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems);
                            if ($problems->isEmpty()) {
                                return [
                                    Placeholder::make('')
                                        ->content(new HtmlString("<span class='text-red-500 font-bold text-l'>Bu imtihonda hech qanday topshiriq yo'q</span>"))
                                ];
                            }

                            $students = $exam->sinf->students->sortBy('full_name');

                            $schema = [];

                            // Create header row
                            $headerC = new HtmlString("<span class='text-green-500 font-bold text-l'>O'quvchi / Topshiriq</span>");
                            $header = [
                                Placeholder::make('')->content(fn () => $headerC),
                            ];

                            foreach ($problems as $problem) {
                                $header[] = Placeholder::make('')
                                    ->content(new HtmlString("<span class='text-green-500 font-bold text-l'>{$problem['id']}-topshiriq (Max: {$problem['max_mark']})</span>"));
                            }

                            $schema[] = Grid::make(count($header))->schema($header);

                            // Create student rows with input fields
                            foreach ($students as $student) {
                                $row = [
                                    Placeholder::make('')
                                        ->content($student->full_name),
                                ];

                                foreach ($problems as $problem) {
                                    // Check if mark already exists
                                    $existingMark = Mark::where('student_id', $student->id)
                                        ->where('problem_id', $problem['id'])
                                        ->where('exam_id', $examId)
                                        ->first();

                                    $row[] = TextInput::make("marks.{$student->id}_{$problem['id']}")
                                        ->hiddenLabel()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue($problem['max_mark'])
                                        ->default($existingMark ? $existingMark->mark : 0);
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
