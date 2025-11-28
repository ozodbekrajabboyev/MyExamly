<?php
// File: app/Livewire/StatisticsPageContent.php
namespace App\Livewire;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Sinf;
use App\Models\Subject;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class StatisticsPageContent extends Component
{
    public ?int $sinfId = null;
    public ?int $subjectId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public $studentsData = [];

    /**
     * The mount method is called when the component is first initialized.
     * We'll set a default date range here (e.g., last 3 months for more data).
     */
    public function mount(): void
    {
        $this->startDate = Carbon::now()->subMonths(3)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    /**
     * Apply filters and load student data
     */
    public function applyFilters(): void
    {
        if (!$this->sinfId || !$this->subjectId) {
            $this->studentsData = [];
            return;
        }

        $this->loadStudentsData();

        // Also dispatch the event for charts if needed
        $this->dispatch('updateStats',
            sinfId: $this->sinfId,
            subjectId: $this->subjectId,
            startDate: $this->startDate,
            endDate: $this->endDate
        );
    }

    /**
     * Load students data with their BSB and CHSB results
     */
    public function loadStudentsData(): void
    {
        $students = Student::where('sinf_id', $this->sinfId)
            ->orderBy('full_name')
            ->get();

        if ($students->isEmpty()) {
            $this->studentsData = [];
            return;
        }

        // First, let's check if we have any exam data at all for this sinf and subject
        $hasAnyExams = DB::table('exams')
            ->where('sinf_id', $this->sinfId)
            ->where('subject_id', $this->subjectId)
            ->exists();

        if (!$hasAnyExams) {
            $this->studentsData = [];
            return;
        }

        // Check if student_exams pivot table has data, if not, try to populate it
        $hasStudentExamData = DB::table('student_exams')
            ->join('exams', 'student_exams.exam_id', '=', 'exams.id')
            ->where('exams.sinf_id', $this->sinfId)
            ->where('exams.subject_id', $this->subjectId)
            ->exists();

        if (!$hasStudentExamData) {
            // Try to populate the pivot table for missing data
            $this->populateStudentExamData();
        }

        $this->studentsData = $students->map(function ($student) {
            // Get BSB exams for this student, subject, and date range
            $bsbExams = $this->getExamResults($student->id, 'BSB');
            $chsbExams = $this->getExamResults($student->id, 'CHSB');

            $bsbAvg = $bsbExams["total_sum"];
            $chsbAvg = $chsbExams["total_sum"];

            $overallTotal = ($bsbAvg + $chsbAvg);

            return [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'bsb' => $bsbAvg,
                'chsb' => $chsbAvg,
                'overall_total' => round($overallTotal, 2)
            ];
        })->toArray();
    }

    /**
     * Populate student exam data for exams that have marks but no pivot data
     */
    private function populateStudentExamData(): void
    {
        $exams = Exam::where('sinf_id', $this->sinfId)
            ->where('subject_id', $this->subjectId)
            ->whereHas('marks')
            ->get();

        foreach ($exams as $exam) {
            // Clear existing potentially wrong pivot data
            \DB::table('student_exams')->where('exam_id', $exam->id)->delete();

            // Recalculate with the fixed logic
            $exam->calculateStudentTotals();
        }
    }

    /**
     * Get exam results for a student by type (BSB or CHSB)
     */
    private function getExamResults($studentId, $examType)
    {
        $query = DB::table('student_exams')
            ->join('exams', 'student_exams.exam_id', '=', 'exams.id')
            ->where('student_exams.student_id', $studentId)
            ->where('exams.subject_id', $this->subjectId)
            ->where('exams.sinf_id', $this->sinfId)
            ->where('exams.type', $examType);

        // Apply date filters
        if ($this->startDate) {
            $query->whereDate('exams.created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('exams.created_at', '<=', $this->endDate);
        }

        // Get results
        $results = $query->select('student_exams.total', 'student_exams.percentage')->get();

        // Calculate total sum
        $totalSum = $results->sum('total');

        // If you need percentage sum (optional)
        $percentageSum = $results->sum('percentage');

        return [
            'results' => $results,
            'total_sum' => $totalSum,
            'percentage_sum' => $percentageSum, // remove if not needed
        ];
    }




    /**
     * Calculate average total and percentage from exam results
     */
    private function calculateAverage($examResults)
    {
        if ($examResults->isEmpty()) {
            return ['total' => 0, 'percentage' => 0];
        }

        $avgTotal = $examResults->avg('total');
        $avgPercentage = $examResults->avg('percentage');

        return [
            'total' => round($avgTotal, 2),
            'percentage' => round($avgPercentage, 2)
        ];
    }

    /**
     * Quick filter method for setting the date range to the last 7 days.
     */
    public function filterLast7Days(): void
    {
        $this->startDate = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->applyFilters(); // Re-apply filters after setting dates
    }

    /**
     * Quick filter method for setting the date range to the last 30 days.
     */
    public function filterLast30Days(): void
    {
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->applyFilters();
    }

    /**
     * Quick filter method for setting the date range to the last 3 months.
     */
    public function filterLast3Months(): void
    {
        $this->startDate = Carbon::now()->subMonths(3)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->applyFilters();
    }



    /**
     * Download PDF of the statistics table
     */
    public function downloadPdf()
    {
        if (empty($this->studentsData) || !$this->sinfId || !$this->subjectId) {
            return;
        }

        // Get additional data for the PDF
        $sinf = Sinf::find($this->sinfId);
        $subject = Subject::find($this->subjectId);

        // Prepare the PDF data
        $pdfData = [
            'studentsData' => $this->studentsData,
            'sinf' => $sinf,
            'subject' => $subject,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'generatedAt' => Carbon::now()->format('d.m.Y H:i'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.statistics-table', $pdfData)
            ->setPaper('a4', 'landscape');

        // Create filename
        $filename = "{$sinf->name}_sinf_{$subject->name}_statistika_" . date('Y-m-d') . ".pdf";

        // Return the PDF download response
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    /**
     * The render method returns the component's view and passes any
     * necessary data to it, like the lists for the dropdowns.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.statistics-page-content', [
            'sinfs' => Sinf::query()->pluck('name', 'id'),
            'subjects' => Subject::query()->pluck('name', 'id'),
        ]);
    }
}
