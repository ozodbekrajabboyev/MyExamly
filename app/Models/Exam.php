<?php

namespace App\Models;

use App\Traits\ScopesSchool;
use App\Services\ExamCalculationService;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms\Get;  // $get uchun
use Filament\Forms\Set;  // $set uchun
use Illuminate\Database\Eloquent\Builder; // Builder uchun
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Exam extends Model
{
    /** @use HasFactory<\Database\Factories\ExamFactory> */
    use HasFactory, ScopesSchool;

    protected $casts = [
        'problems' => 'array', // Cast JSONB to array
    ];

    protected $fillable = [
        'maktab_id', 'sinf_id', 'subject_id', 'teacher_id', 'teacher2_id',
        'type', 'serial_number', 'quarter', 'metod_id', 'problems'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($exam) {
            do {
                $code = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            } while (self::where('code', $code)->exists());

            $exam->code = $code;
        });
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_exams')
            ->withPivot(['total', 'percentage'])
            ->withTimestamps();
    }


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

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function metod():BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'metod_id');
    }

    public function teacher2(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher2_id');
    }

    public function addProblem($id, $maxMark)
    {
        $problems = $this->problems ?? [];
        $problems[] = [
            'id' => $id,
            'max_mark' => $maxMark
        ];
        $this->problems = $problems;
        $this->save();
    }

    public function getProblem($problemId)
    {
        $problems = $this->problems ?? [];
        return collect($problems)->firstWhere('id', $problemId);
    }

    // Method to get all problems
    public function getProblems()
    {
        return $this->problems ?? [];
    }

    public function updateProblem($problemId, $maxMark)
    {
        $problems = $this->problems ?? [];
        foreach ($problems as &$problem) {
            if ($problem['id'] == $problemId) {
                $problem['max_mark'] = $maxMark;
                break;
            }
        }
        $this->problems = $problems;
        $this->save();
    }

    public function getProblemsCountAttribute()
    {
        return count($this->problems ?? []);
    }

    // Add helper method to get total max marks
    public function getTotalMaxMarks()
    {
        if (empty($this->problems)) {
            return 0;
        }

        return collect($this->problems)
            ->sum(function ($problem) {
                return (float) $problem['max_mark'];
            });
    }

    /**
     * Calculate and store totals for all students in this exam
     * Now uses ExamCalculationService for consistent calculations
     */
    public function calculateStudentTotals()
    {
        $students = Student::where('sinf_id', $this->sinf_id)->get();

        foreach ($students as $student) {
            // Use centralized calculation service for consistency
            $calculation = ExamCalculationService::calculateStudentScore($student, $this);

            // Update or create the pivot record
            $this->students()->syncWithoutDetaching([
                $student->id => [
                    'total' => $calculation['total'],
                    'percentage' => $calculation['percentage'],
                    'updated_at' => now(),
                ]
            ]);
        }
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

                    // Modern checkbox for secondary teacher option
                    Forms\Components\Checkbox::make('has_secondary_teacher')
                        ->label('Ikkinchi o\'qituvchi kerakmi?')
                        ->helperText('Agar imtihonda ikkinchi o\'qituvchi ishtirok etsa, belgilang')
                        ->columnSpanFull()
                        ->live()
                        ->dehydrated(false)
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (!$state) {
                                $set('teacher2_id', null);
                            }
                        }),

                    // Secondary teacher selection (conditionally visible)
                    Select::make('teacher2_id')
                        ->label('Ikkinchi o\'qituvchini tanlang')
                        ->relationship('teacher2', 'full_name')
                        ->options(function () {
                            if(auth()->user()->role_id === 1){
                                $currentTeacherId = auth()->user()->teacher->id;
                            }else{
                                $currentTeacherId = null;
                            }


                            // Get all subject IDs that the current teacher teaches
                            $currentTeacherSubjects = \DB::table('teacher_subject')
                                ->where('teacher_id', $currentTeacherId)
                                ->pluck('subject_id')
                                ->toArray();

                            if (empty($currentTeacherSubjects)) {
                                return [];
                            }

                            return \App\Models\Teacher::where('maktab_id', auth()->user()->maktab_id)
                                ->where('id', '!=', $currentTeacherId)
                                ->whereExists(function ($query) use ($currentTeacherSubjects) {
                                    $query->select(\DB::raw(1))
                                        ->from('teacher_subject')
                                        ->whereColumn('teacher_subject.teacher_id', 'teachers.id')
                                        ->whereIn('teacher_subject.subject_id', $currentTeacherSubjects);
                                })
                                ->pluck('full_name', 'id');
                        })
                        ->columnSpanFull()
                        ->visible(fn (Get $get): bool => $get('has_secondary_teacher'))
                        ->required(fn (Get $get): bool => $get('has_secondary_teacher'))
                        ->placeholder('Ikkinchi o\'qituvchini tanlang'),


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
                        ->numeric()
                        ->rules([
                            function (Get $get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $sinfId = $get('sinf_id');
                                    $subjectId = $get('subject_id');
                                    $type = $get('type');
                                    $recordId = $get('id'); // For edit mode

                                    if ($sinfId && $subjectId && $type && $value) {
                                        $query = \App\Models\Exam::where('sinf_id', $sinfId)
                                            ->where('subject_id', $subjectId)
                                            ->where('type', $type)
                                            ->where('serial_number', $value);

                                        // Exclude current record when editing
                                        if ($recordId) {
                                            $query->where('id', '!=', $recordId);
                                        }

                                        if ($query->exists()) {
                                            $fail('Bu sinf, fan va imtihon turi uchun bunday tartib raqamli imtihon allaqachon mavjud.');
                                        }
                                    }
                                };
                            }
                        ]),

                    Select::make('quarter')
                        ->label('Chorakni tanlang')
                        ->options([
                            'I' => 'I chorak',
                            'II' => 'II chorak',
                            'III' => 'III chorak',
                            'IV' => 'IV chorak'
                        ])
                        ->columnSpanFull()
                        ->nullable()
                        ->placeholder('Chorak tanlang'),

                    Select::make('metod_id')
                        ->label('Metodbirlashma rahbarini tanlang')
                        ->relationship('metod', 'full_name')
                        ->options(function () {
                            return \App\Models\Teacher::where('maktab_id', auth()->user()->maktab_id)
                                ->pluck('full_name', 'id');
                        })
                        ->columnSpanFull()
                        ->required(),

                    \Filament\Forms\Components\Repeater::make('problems')
                        ->label('Topshiriqlar')
                        ->schema([
                            TextInput::make('id')
                                ->label('Topshiriq T/R')
                                ->disabled()
                                ->dehydrated(true),
                            TextInput::make('max_mark')
                                ->numeric()
                                ->required()
                                ->label('Maksimal ball'),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->default([])
                        ->minItems(0)
                        ->afterStateUpdated(function ($state, Get $get, Set $set) {
                            // Agar bo'sh bo'lsa hech narsa qilmaymiz
                            if (empty($state)) {
                                return;
                            }

                            // IDlarni qaytadan ketma-ket raqamlaymiz
                            $problems = collect($state)
                                ->values()
                                ->map(function ($problem, $index) {
                                    $problem['id'] = $index + 1;
                                    return $problem;
                                })
                                ->toArray();

                            $set('problems', $problems);
                        })
                        ->addAction(
                            fn (\Filament\Forms\Components\Actions\Action $action) => $action
                                ->action(function (array $data, \Filament\Forms\Components\Repeater $component, Get $get, Set $set) {
                                    $currentProblems = $get('problems') ?? [];

                                    $newProblem = [
                                        'id' => count($currentProblems) + 1,
                                        'max_mark' => null,
                                    ];

                                    $currentProblems[] = $newProblem;
                                    $set('problems', $currentProblems);
                                })
                                ->label('Topshiriq qo‘shish')
                        ),



                ]),
        ];
    }
}
