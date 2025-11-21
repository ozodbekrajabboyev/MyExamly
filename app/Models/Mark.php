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

function createGroupedProblemsForm($students, \Illuminate\Support\Collection $problems, mixed $examId)
{
    $problemsGrouped = $problems->chunk(6); // Group into chunks of 5
    $schema = [];

    foreach ($problemsGrouped as $groupIndex => $problemGroup) {
        $schema[] = Section::make("Topshiriqlar guruhi " . ($groupIndex + 1))
            ->schema([
                Grid::make($problemGroup->count() + 1)
                    ->schema(function () use ($students, $problemGroup, $examId) {
                        $groupSchema = [];

                        // Header for this group
                        $header = [Placeholder::make('')->content(new HtmlString("<span class='font-bold text-l'>O'quvchi/Topshiriq</span>"))];
                        foreach ($problemGroup as $problem) {
                            $header[] = Placeholder::make('')
                                ->content(new HtmlString("<span class='font-bold'>{$problem['id']}-(Max: {$problem['max_mark']})<span>"));
                        }
                        $groupSchema[] = Grid::make(count($header))->schema($header);

                        // Student rows for this group
                        foreach ($students as $student) {
                            $row = [Placeholder::make('')->content($student->full_name)];

                            foreach ($problemGroup as $problem) {
                                $existingMark = Mark::where('student_id', $student->id)
                                    ->where('problem_id', $problem['id'])
                                    ->where('exam_id', $examId)
                                    ->first();

                                $row[] = TextInput::make("marks.{$student->id}_{$problem['id']}")
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue($problem['max_mark'])
                                    ->validationMessages([
                                        'min' => 'Kiritilgan baho kamida :min bo‘lishi kerak.',
                                        'max' => 'Kiritilgan baho :max dan oshmasligi kerak.'
                                    ])
                                    ->default($existingMark ? $existingMark->mark : 0)
                                    ->extraAttributes(['class' => 'text-center text-sm w-16']);
                            }

                            $groupSchema[] = Grid::make(count($row))->schema($row);
                        }

                        return $groupSchema;
                    })
            ])
            ->collapsible()
            ->collapsed(false);
    }

    return $schema;

}

class Mark extends Model
{
    /** @use HasFactory<\Database\Factories\MarkFactory> */
    use HasFactory, ScopesSchool;

    public function getMarkAttribute($value)
    {
        // If it's a whole number like 6.00 → return 6 (int)
        if (floor($value) == $value) {
            return (int) $value;
        }

        // Otherwise return as float (e.g. 2.50 → 2.5)
        return (float) $value;
    }

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
//                                ->whereNotNull('problems') // Only exams with problems
                                ->whereDoesntHave('marks')   // Only exams without marks
                                ->with(['sinf', 'subject']);

                            // If teacher should only see their own exams
                            if ($user->role->name === 'teacher') {
                                $query->where(function ($q) use ($user) {
                                    $q->where('teacher_id', $user->teacher->id)
                                        ->orWhere('teacher2_id', $user->teacher->id);
                                });
                            }

                            return $query->get()
                                ->filter(function ($exam) {
                                    // Additional check to ensure problems JSON is valid and not empty
                                    $problems = is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems;
                                    return is_array($problems);
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
                            if (!$exam) return [];

                            $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems);
                            if ($problems->isEmpty()) {
                                return [
                                    \Filament\Forms\Components\Placeholder::make('')
                                        ->content(new HtmlString('
                                        <div class="flex flex-col items-center justify-center p-10 border-2 border-dashed border-blue-200 rounded-2xl bg-gradient-to-br from-white to-gray-50 shadow-lg text-center transition-all hover:shadow-xl">
                                            <br><br>
                                            <div class="flex items-center justify-center w-12 h-12 mb-4 bg-blue-100 rounded-full">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <p class="text-xl font-bold text-gray-800 mb-2">Ushbu imtihon uchun topshiriqlar mavjud emas.</p>
                                            <p class="text-md text-gray-600 max-w-xs">Baholash jarayonini amalga oshirish uchun, iltimos, avvalo imtihoningizga topshiriqlar qo‘shing. ✨</p><br>
                                        </div>
                                    ')),
                                ];
                            }



                            $students = $exam->sinf->students->sortBy('full_name');

                            // If too many problems, group them
                            if ($problems->count() > 7) {
                                return createGroupedProblemsForm($students, $problems, $examId);
                            }

                            // Original grid approach for smaller datasets
                            $schema = [];
                            $columnCount = $problems->count() + 1; // +1 for student name column

                            // Create header row
                            $headerC = new HtmlString("<span class='text-green-500 font-bold text-l'>O'quvchi/Topshiriq</span>");
                            $header = [Placeholder::make('')->content(fn () => $headerC)];

                            foreach ($problems as $problem) {
                                $header[] = Placeholder::make('')
                                    ->content(new HtmlString("<span class='text-green-500 font-bold text-xs sm:text-sm'>{$problem['id']}-<span class='text-xs'>(Max: {$problem['max_mark']})</span></span>"));
                            }

                            $schema[] = Grid::make($columnCount)->schema($header);

                            // Create student rows
                            foreach ($students as $student) {
                                $row = [
                                    Placeholder::make('')
                                        ->content(new HtmlString(
                                            "<span class='font-medium font-bold text-l'>" .
                                            $student->extractFirstAndLastName()['first'] . ' ' .
                                            $student->extractFirstAndLastName()['last'] .
                                            "</span>"
                                        ))
                                ];

                                foreach ($problems as $problem) {
                                    $existingMark = Mark::where('student_id', $student->id)
                                        ->where('problem_id', $problem['id'])
                                        ->where('exam_id', $examId)
                                        ->first();

                                    $row[] = TextInput::make("marks.{$student->id}_{$problem['id']}")
                                        ->hiddenLabel()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue($problem['max_mark'])
                                        ->validationMessages([
                                            'min' => 'Kiritilgan baho kamida :min bo‘lishi kerak.',
                                            'max' => 'Kiritilgan baho :max dan oshmasligi kerak.'
                                        ])
                                        ->default($existingMark ? $existingMark->mark : 0)
                                        ->extraAttributes(['class' => 'text-center text-sm w-16']);
                                }

                                $schema[] = Grid::make($columnCount)->schema($row);
                            }

                            return $schema;
                        })
                        ->extraAttributes([
                            'class' => 'mark-table overflow-x-auto',
                            'style' => 'max-width: 100vw;'
                        ])

                ]),
        ];
    }

    /**
     * Update the student_exams pivot table with calculated totals and percentages
     * when marks are saved or updated
     */
    protected static function booted()
    {
        static::saved(function ($mark) {
            $mark->updateStudentExamTotals();
        });

        static::deleted(function ($mark) {
            $mark->updateStudentExamTotals();
        });
    }

    /**
     * Update the student_exams pivot table for this mark's student and exam
     */
    public function updateStudentExamTotals()
    {
        $exam = Exam::find($this->exam_id);
        $student = Student::find($this->student_id);

        if (!$exam || !$student) {
            return;
        }

        // Calculate total score for this student in this exam
        $totalScore = Mark::where('exam_id', $this->exam_id)
            ->where('student_id', $this->student_id)
            ->sum('mark');

        // Get exam problems and calculate total max score
        $problems = $exam->problems ?? [];
        $totalMaxScore = collect($problems)->sum('max_mark');

        // Calculate percentage
        $percentage = $totalMaxScore > 0 ? round(($totalScore / $totalMaxScore) * 100, 2) : 0;

        // Update or create the pivot record
        $exam->students()->syncWithoutDetaching([
            $this->student_id => [
                'total' => $totalScore,
                'percentage' => $percentage,
                'updated_at' => now(),
            ]
        ]);
    }
}
