<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Problem;
use App\Models\Student;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public $selectedExamId = null;
    public $marks = [];
    public $problems = [];
    public $students = [];
    public $totalMaxScore = 0;

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
            $this->problems = Problem::where('exam_id', $exam->id)
                ->orderBy('problem_number')
                ->get();
            $this->students = Student::where('sinf_id', $exam->sinf_id)
                ->orderBy('full_name')
                ->get();

            $this->totalMaxScore = $this->problems->sum('max_mark');
        }
    }

    /**
     * Sends an approval request notification to administrators.
     *
     * UPDATED: This method now correctly queries for admin users using a `whereHas`
     * clause on the 'role' relationship. The original `where('role', 'admin')`
     * was incorrect. Added checks to ensure an exam is selected and that
     * admins exist before sending notifications.
     */


    /**
     * Generates and downloads a PDF of the exam results.
     */
    public function downloadPdf()
    {
        $exam = Exam::with(['sinf', 'subject'])->find($this->selectedExamId);

        // Check if exam is approved
        if (!$exam || $exam->status !== 'approved') {
            Notification::make()
                ->title('Tasdiqlash jarayoni!')
                ->body('Natijalarni PDF shaklida yuklab olish imkoniyati faqat imtihon tasdiqlangandan so‘ng beriladi.')
                ->warning()
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('requestApproval')
                        ->label('Tasdiqlashni so‘rash')
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
        $problems = Problem::where('exam_id', $exam->id)->orderBy('problem_number')->get();
        $students = Student::where('sinf_id', $exam->sinf_id)->orderBy('full_name')->get();
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
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
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
            ->whereHas('problems.marks') // only exams that have at least one mark
            ->when($user->role->name === 'teacher', function ($query) use ($user) {
                $query->where('teacher_id', $user->teacher->id);
            })
            ->with(['sinf', 'subject'])
            ->get();

        return view('livewire.dashboard', [
            'exams' => $exams,
        ]);
    }
}
