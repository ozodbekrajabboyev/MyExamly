<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\User;
use App\Services\ExamCalculationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class Dashboard extends Component implements HasForms
{

    use InteractsWithForms;

    protected $listeners = ['refreshTable' => 'refreshTableData'];

    public $selectedExamId = null;
    public $marks = [];
    public $problems = [];
    public $students = [];
    public $totalMaxScore = 0;


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('selectedExamId')
                    ->label('Imtihon tanlang')
                    ->placeholder('-- Imtihonni tanlang --')
                    ->options(function () {
                        $user = Auth::user();
                        return \App\Models\Exam::query()
                            ->whereHas('marks')
                            ->when($user->role->name === 'teacher', function ($query) use ($user) {
                                $query->where(function ($q) use ($user) {
                                    $q->where('teacher_id', $user->teacher->id)
                                        ->orWhere('teacher2_id', $user->teacher->id);
                                });
                            })
                            ->with(['sinf', 'subject'])
                            ->get()
                            ->mapWithKeys(function ($exam) {
                                $subject = $exam->subject->name ?? 'No Subject';
                                $class = $exam->sinf->name ?? 'NO SINF';
                                return [$exam->id => "{$class} sinf | {$subject} | {$exam->serial_number}-{$exam->type}"];
                            });
                    })
                    ->searchable(),
            ]);
    }

    public function generateTable()
    {
        if (!$this->selectedExamId) {
            $this->marks = [];
            $this->problems = [];
            $this->students = [];
            $this->totalMaxScore = 0;
            return;
        }

        $exam = Exam::find($this->selectedExamId);

        if ($exam) {
            $this->marks = Mark::where('exam_id', $exam->id)->get();

            // Fix: Handle problems as JSON array, not objects
            $problemsData = is_string($exam->problems) ? json_decode($exam->problems, true) : ($exam->problems ?? []);
            $this->problems = collect($problemsData)
                ->sortBy('id') // Sort by id instead of problem_number
                ->values();

            // SAFE APPROACH: Only recalculate if pivot data is missing or inconsistent
            $studentsWithPivot = Student::where('sinf_id', $exam->sinf_id)
                ->whereHas('exams', function ($query) use ($exam) {
                    $query->where('exam_id', $exam->id);
                })
                ->with(['exams' => function ($query) use ($exam) {
                    $query->where('exam_id', $exam->id);
                }])
                ->get();

            // If no students have pivot data, calculate it for the first time
            if ($studentsWithPivot->isEmpty() && $this->marks->isNotEmpty()) {
                // This is a one-time calculation for exams that haven't been processed yet
                $exam->calculateStudentTotals();

                // Reload students with fresh pivot data
                $studentsWithPivot = Student::where('sinf_id', $exam->sinf_id)
                    ->whereHas('exams', function ($query) use ($exam) {
                        $query->where('exam_id', $exam->id);
                    })
                    ->with(['exams' => function ($query) use ($exam) {
                        $query->where('exam_id', $exam->id);
                    }])
                    ->orderBy('full_name')
                    ->get();
            }

            $this->students = $studentsWithPivot->isNotEmpty() ? $studentsWithPivot :
                             Student::where('sinf_id', $exam->sinf_id)->orderBy('full_name')->get();

            $this->totalMaxScore = collect($this->problems)->sum('max_mark');
        }
    }

    /**
     * Force recalculation of all pivot data for debugging
     * Now uses centralized calculation service
     */
    public function forceRecalculateAll()
    {
        if (!$this->selectedExamId) {
            return;
        }

        $exam = Exam::find($this->selectedExamId);
        if ($exam) {
            $students = Student::where('sinf_id', $exam->sinf_id)->get();

            foreach ($students as $student) {
                $calculation = ExamCalculationService::calculateStudentScore($student, $exam);
                $exam->students()->syncWithoutDetaching([
                    $student->id => [
                        'total' => $calculation['total'],
                        'percentage' => $calculation['percentage'],
                        'updated_at' => now(),
                    ]
                ]);
            }

            // Refresh the table
            $this->generateTable();
        }
    }

    /**
     * Generates and downloads a PDF of the exam results.
     * Uses cached calculations to prevent value changes during PDF generation.
     */
    public function downloadPdf()
    {
        $this->refreshTableData();
        $exam = Exam::with(['sinf', 'subject'])->find($this->selectedExamId);

        // Check if exam is approved
        if (!$exam || $exam->status !== 'approved') {
            Notification::make()
                ->title('Tasdiqlash jarayoni!')
                ->body("Natijalarni PDF shaklida yuklab olish imkoniyati faqat imtihon tasdiqlangandan so'ng beriladi.")
                ->warning()
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('requestApproval')
                        ->label("Tasdiqlashni so'rash")
                        ->button()
                ->color('warning')
                ->dispatch('requestApproval', [$exam->id])
                ->close()
                ])
                ->send();

            return;
        }

        // Fetch the necessary data for the PDF
        $marks = Mark::where('exam_id', $exam->id)->get();

        // Fix: Handle problems as JSON array
        $problemsData = is_string($exam->problems) ? json_decode($exam->problems, true) : ($exam->problems ?? []);
        $problems = collect($problemsData)->sortBy('id')->values();

        // CRITICAL FIX: Load students with their EXISTING pivot data - DO NOT RECALCULATE OR UPDATE
        $students = Student::where('sinf_id', $exam->sinf_id)
            ->with(['exams' => function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            }])
            ->orderBy('full_name')
            ->get();

        // DO NOT update pivot data during PDF generation - use existing data only

        $totalMaxScore = $problems->sum('max_mark');

        // Prepare the PDF
        $pdf = Pdf::loadView('pdf.dashboard-table', [
            'exam' => $exam,
            'students' => $students,
            'problems' => $problems,
            'totalMaxScore' => $totalMaxScore,
            'marks' => $marks,
        ])->setPaper('a4', 'landscape');

        // Prepare the filename
        $className = $exam->sinf->name;
        $subject = $exam->subject->name;
        $type1 = $exam->serial_number . "-" . $exam->type;
        $filename = "$className -sinf | $subject | $type1 | results.pdf";

        // Return the response to download the file in the browser
        $response = response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);

        // Refresh the dashboard table to ensure consistent display
        $this->dispatch('refreshTable');

        return $response;
    }

    /**
     * Refresh table data without triggering recalculation
     */
    public function refreshTableData()
    {
        if (!$this->selectedExamId) {
            return;
        }

        $exam = Exam::find($this->selectedExamId);
        if ($exam) {
            // Simply reload the display data without any calculation changes
            $this->marks = Mark::where('exam_id', $exam->id)->get();

            $problemsData = is_string($exam->problems) ? json_decode($exam->problems, true) : ($exam->problems ?? []);
            $this->problems = collect($problemsData)->sortBy('id')->values();

            // Load students with their existing pivot data - DO NOT MODIFY
            $this->students = Student::where('sinf_id', $exam->sinf_id)
                ->with(['exams' => function ($query) use ($exam) {
                    $query->where('exam_id', $exam->id);
                }])
                ->orderBy('full_name')
                ->get();

            $this->totalMaxScore = collect($this->problems)->sum('max_mark');
        }
    }

    #[On('requestApproval')]
    public function handleRequestApproval($examId)
    {
        $exam = Exam::find($this->selectedExamId);

        // Correctly query for admin users using the 'role' relationship.
        $admins = User::where('role_id', 2)
            ->where('maktab_id', auth()->user()->maktab_id)
            ->get();

        if ($admins->isEmpty()) {
            Notification::make()
                ->title('Action Failed')
                ->body('Could not send approval request. No administrators found.')
                ->danger()
                ->send();
            return;
        }

        // Send notification to all found admins
        Notification::make()
            ->title('Imtihonni tasdiqlash so‘rovi')
            ->body("{$exam->sinf->name}-sinf | {$exam->subject->name} | {$exam->serial_number}-{$exam->type} imtihonini tasdiqlash uchun so‘rov yuborildi.")
            ->icon('heroicon-o-document-check')
            ->iconColor('warning')
            ->actions([
                \Filament\Notifications\Actions\Action::make('edit_exam')
                    ->label('👉 Imtihonni tasdiqlash')
                    ->url(route('filament.app.resources.exams.edit', ['record' => $exam->id]))
                    ->button()
                    ->close()
                    ->color('primary')
            ])
            ->sendToDatabase($admins);

        // Show success notification to the current user
        Notification::make()
            ->title('So‘rov yuborildi')
            ->body('Tasdiqlash so‘rovingiz administratorga muvaffaqiyatli yuborildi.')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->send();
    }

    public function render()
    {
        $user = Auth::user();

        $exams = \App\Models\Exam::query()
            ->whereHas('marks') // only exams that have marks
            ->when($user->role->name === 'teacher', function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('teacher_id', $user->teacher->id)
                        ->orWhere('teacher2_id', $user->teacher->id);
                });
            })
            ->with(['sinf', 'subject'])
            ->get();
        return view('livewire.dashboard', [
            'exams' => $exams,
        ]);
    }
}
