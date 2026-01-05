<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Http\Requests\StoreMarksRequest;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Services\MarkCalculationService;
use App\Services\MarkService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ManageMarks extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $resource = ExamResource::class;
    protected static string $view = 'filament.resources.exam-resource.pages.manage-marks-table';
    protected static ?string $title = 'Baholarni boshqarish';

    public ?Exam $exam = null;
    public Collection $students;
    public array $problems = [];

    public function mount(Request $request): void
    {
        $examId = $request->query('exam_id');

        if (!$examId) {
            Notification::make()
                ->title('Xato')
                ->body('Imtihon ID si ko\'rsatilmagan.')
                ->danger()
                ->send();

            $this->redirect(ExamResource::getUrl('index'));
            return;
        }

        $this->exam = Exam::with(['sinf', 'subject', 'teacher', 'teacher2'])->find($examId);

        if (!$this->exam) {
            Notification::make()
                ->title('Xato')
                ->body('Imtihon topilmadi.')
                ->danger()
                ->send();

            $this->redirect(ExamResource::getUrl('index'));
            return;
        }

        // Check authorization
        $user = auth()->user();
        if ($user->role->name === 'teacher' &&
            $this->exam->teacher_id !== $user->teacher->id &&
            $this->exam->teacher2_id !== $user->teacher->id) {
            Notification::make()
                ->title('Ruxsat yo\'q')
                ->body('Sizda bu imtihonning baholarini boshqarish huquqi yo\'q.')
                ->danger()
                ->send();

            $this->redirect(ExamResource::getUrl('index'));
            return;
        }

        $this->students = Student::where('sinf_id', $this->exam->sinf_id)
            ->where('maktab_id', $this->exam->maktab_id)
            ->orderBy('full_name')
            ->get();

        $this->problems = $this->exam->getProblems();

        // Ensure marks exist for all students and problems
        $this->ensureMarksExist();
    }

    protected function ensureMarksExist(): void
    {
        $markService = new MarkService();
        // First clean up any orphaned marks, then create missing marks
        $markService->cleanupOrphanedMarks($this->exam);
        $markService->createMarksForExam($this->exam);
    }

    public function table(Table $table): Table
    {
        // Note: We can't cache marks lookup here because it needs to refresh after updates
        // Each getStateUsing callback will fetch fresh data to ensure consistency

        $columns = [
            Tables\Columns\TextColumn::make('full_name')
                ->label('O\'quvchi IFSH')
                ->weight('medium')
                ->width('200px')
                ->wrap()
                ->extraAttributes([
                    'class' => 'sticky left-0 bg-white dark:bg-gray-800 dark:text-gray-100 z-10 border-r border-gray-200 dark:border-gray-600 student-name-column'
                ])
        ];

        // Dynamically add columns for each problem
        foreach ($this->problems as $problem) {
            $columns[] = Tables\Columns\TextInputColumn::make("problem_{$problem['id']}")
                ->label("T-{$problem['id']}\n(Max: {$problem['max_mark']})")
                ->getStateUsing(function ($record) use ($problem) {
                    // Fetch fresh mark data to avoid stale cache issues
                    $mark = Mark::where('student_id', $record->id)
                        ->where('exam_id', $this->exam->id)
                        ->where('problem_id', $problem['id'])
                        ->first();
                    return $mark ? $mark->mark : 0;
                })
                ->updateStateUsing(function ($record, $state) use ($problem) {
                    // Log for debugging
                    \Log::info("Updating mark for student {$record->id}, problem {$problem['id']}, new value: {$state}");

                    // Create and validate using StoreMarksRequest
                    $request = new StoreMarksRequest();
                    $request->merge([
                        'mark' => $state,
                        'student_id' => $record->id,
                        'exam_id' => $this->exam->id,
                        'problem_id' => $problem['id'],
                        'max_mark' => $problem['max_mark']
                    ]);

                    $validator = validator($request->all(), $request->rules(), $request->messages());

                    if ($validator->fails()) {
                        Notification::make()
                            ->title('Xato')
                            ->body($validator->errors()->first())
                            ->danger()
                            ->send();
                        return $this->getStateForColumn($record, $problem);
                    }

                    // Update or create the mark with proper transaction handling
                    try {
                        $updatedMark = null;
                        DB::transaction(function () use ($record, $state, $problem, &$updatedMark) {
                            $updatedMark = Mark::updateOrCreate([
                                'student_id' => $record->id,
                                'exam_id' => $this->exam->id,
                                'problem_id' => $problem['id'],
                            ], [
                                'mark' => (float) $state,
                                'maktab_id' => $this->exam->maktab_id,
                                'sinf_id' => $this->exam->sinf_id,
                            ]);
                        });

                        \Log::info("Mark saved successfully: {$updatedMark->mark}");

                        Notification::make()
                            ->title('Saqlandi')
                            ->body("T-{$problem['id']} uchun baho saqlandi")
                            ->success()
                            ->duration(2000)
                            ->send();

                        // Return the actual saved value from database to ensure UI consistency
                        $returnValue = $updatedMark ? (float) $updatedMark->mark : (float) $state;
                        \Log::info("Returning value: {$returnValue}");
                        return $returnValue;
                    } catch (\Exception $e) {
                        \Log::error("Error saving mark: " . $e->getMessage());

                        Notification::make()
                            ->title('Xato')
                            ->body('Bahoni saqlashda xatolik yuz berdi: ' . $e->getMessage())
                            ->danger()
                            ->send();

                        return $this->getStateForColumn($record, $problem);
                    }
                })
                ->afterStateUpdated(function ($record, $state) use ($problem) {
                    // Dispatch events to update UI and totals
                    $this->dispatch('mark-updated', [
                        'studentId' => $record->id,
                        'problemId' => $problem['id'],
                        'newMark' => $state
                    ]);
                })
                ->rules(function () use ($problem) {
                    return StoreMarksRequest::getMarkRules($problem['max_mark']);
                })
                ->extraAttributes([
                    'class' => 'text-center mark-input problem-column',
                    'data-problem-id' => $problem['id'],
                    'x-on:input' => 'updateTotal($event.target)',
                    'x-on:change' => 'setTimeout(() => updateTotal($event.target), 500)'
                ])
                ->alignment('center')
                ->width('100px')
                ->type('number')
                ->step(0.1);
        }

        // Add Total column (optimized calculation using pre-loaded data)
        $totalMaxMarks = collect($this->problems)->sum('max_mark');
        $columns[] = Tables\Columns\TextColumn::make('total_marks')
            ->label("Jami\n(Max: {$totalMaxMarks})")
            ->getStateUsing(function ($record) {
                // Calculate total from fresh marks data to avoid stale cache
                $marks = Mark::where('student_id', $record->id)
                    ->where('exam_id', $this->exam->id)
                    ->whereIn('problem_id', collect($this->problems)->pluck('id'))
                    ->get();

                $total = $marks->sum('mark');
                return number_format($total, 1);
            })
            ->extraAttributes([
                'class' => 'text-center font-bold bg-blue-50 dark:bg-blue-900/20 total-column sticky right-0 z-5'
            ])
            ->alignment('center')
            ->width('100px')
            ->color('primary');


        return $table
            ->query(
                Student::query()
                    ->where('sinf_id', $this->exam->sinf_id)
                    ->where('maktab_id', $this->exam->maktab_id)
                    ->orderBy('full_name')
            )
            ->columns($columns)
            ->striped()
            ->paginated(false)
            ->heading('Baholar jadvali')
            ->description("Har bir katakchani bosib bahoni to'g'ridan-to'g'ri tahrirlang. Baholar avtomatik saqlanadi. Mobil qurilmalarda chapga-o'nga surib barcha ustunlarni ko'ring.")
            ->headerActions([
                Tables\Actions\Action::make('save_marks')
                    ->label('Baholarni saqlash')
                    ->icon('heroicon-o-check')
                    ->action(function () {
                        Notification::make()
                            ->title('Muvaffaqiyat')
                            ->body('Barcha baholar saqlandi')
                            ->success()
                            ->send();
                    })
                    ->color('success'),
            ]);
    }

    protected function getStateForColumn($record, $problem)
    {
        // Get the current mark from database for consistency
        $mark = Mark::where('student_id', $record->id)
            ->where('exam_id', $this->exam->id)
            ->where('problem_id', $problem['id'])
            ->first();

        $value = $mark ? (float) $mark->mark : 0.0;
        \Log::info("getStateForColumn returning: {$value} for student {$record->id}, problem {$problem['id']}");
        return $value;
    }

    protected function getActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label('Orqaga')
                ->url(ExamResource::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
