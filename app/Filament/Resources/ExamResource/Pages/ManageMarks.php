<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Services\MarkService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageMarks extends Page
{
    protected static string $resource = ExamResource::class;
    protected static string $view = 'filament.resources.exam-resource.pages.manage-marks-table';
    protected static ?string $title = 'Baholarni boshqarish';

    public ?Exam $exam = null;
    public Collection $students;
    public array $problems = [];
    public array $marks = [];

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

        // Load existing marks into the $marks array
        $this->loadMarks();
    }

    protected function ensureMarksExist(): void
    {
        $markService = new MarkService();
        $markService->cleanupOrphanedMarks($this->exam);
        $markService->createMarksForExam($this->exam);
    }

    protected function loadMarks(): void
    {
        // Initialize all marks to 0
        foreach ($this->students as $student) {
            foreach ($this->problems as $problem) {
                $this->marks[$student->id . '_' . $problem['id']] = 0;
            }
        }

        // Overwrite with actual DB values
        $existingMarks = Mark::where('exam_id', $this->exam->id)
            ->whereIn('student_id', $this->students->pluck('id'))
            ->whereIn('problem_id', collect($this->problems)->pluck('id'))
            ->get();

        foreach ($existingMarks as $mark) {
            $this->marks[$mark->student_id . '_' . $mark->problem_id] = $mark->mark;
        }
    }

    public function saveAll(): void
    {
        $markService = new MarkService();
        $errors = $markService->validateMarks($this->exam, $this->marks);

        if (!empty($errors)) {
            $firstErrorKey = array_key_first($errors);
            [$studentId, $problemId] = explode('_', $firstErrorKey);
            $student = $this->students->firstWhere('id', (int) $studentId);
            $studentName = $student ? $student->full_name : "O'quvchi #{$studentId}";

            Notification::make()
                ->title('Xato')
                ->body("{$studentName}, T-{$problemId}: " . $errors[$firstErrorKey])
                ->danger()
                ->send();
            return;
        }

        DB::transaction(function () {
            Mark::withoutEvents(function () {
                foreach ($this->marks as $key => $markValue) {
                    [$studentId, $problemId] = explode('_', $key);

                    Mark::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'exam_id' => $this->exam->id,
                            'problem_id' => $problemId,
                        ],
                        [
                            'mark' => (float) ($markValue ?? 0),
                            'maktab_id' => $this->exam->maktab_id,
                            'sinf_id' => $this->exam->sinf_id,
                        ]
                    );
                }
            });
        });

        // Recalculate all student totals once
        $this->exam->calculateStudentTotals();

        Notification::make()
            ->title('Muvaffaqiyat')
            ->body('Barcha baholar saqlandi')
            ->success()
            ->send();
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
