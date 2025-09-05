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
    $problemsGrouped = $problems->chunk(7); // Group into chunks of 5
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
                                    ->default($existingMark ? $existingMark->mark : 0);
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

                            $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems);
                            if ($problems->isEmpty()) {
                                return [
                                    Placeholder::make('')
                                        ->content(new HtmlString("<span class='text-red-500 font-bold text-l'>Bu imtihonda hech qanday topshiriq yo'q</span>"))
                                ];
                            }

                            $students = $exam->sinf->students->sortBy('full_name');

                            // If too many problems, group them
                            if ($problems->count() > 10) {
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
                                        ->content(new HtmlString("<span class='font-medium font-bold text-l'>{$student['full_name']}</span>"))
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
}
