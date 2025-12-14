<?php
// File: app/Livewire/StatisticsPageContent.php
namespace App\Livewire;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Sinf;
use App\Models\Subject;
use App\Models\Student;
use App\Services\QuarterStatisticsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class StatisticsPageContent extends Component
{
    public ?int $sinfId = null;
    public ?int $subjectId = null;
    public ?string $quarter = null;
    public $studentsData = [];
    public $summary = [];

    /**
     * Apply filters and load student data
     */
    public function applyFilters(): void
    {
        if (!$this->sinfId || !$this->subjectId) {
            $this->studentsData = [];
            $this->summary = [];
            return;
        }

        $this->loadStudentsData();

        // Dispatch the event for charts
        $this->dispatch('updateStats',
            sinfId: $this->sinfId,
            subjectId: $this->subjectId,
            quarter: $this->quarter
        );
    }

    /**
     * Load students data with their BSB and CHSB results using QuarterStatisticsService
     */
    public function loadStudentsData(): void
    {
        $result = QuarterStatisticsService::getQuarterStatistics(
            $this->sinfId,
            $this->subjectId,
            $this->quarter
        );

        $this->studentsData = $result['students_data'];
        $this->summary = $result['summary'];
    }

    /**
     * Populate student exam data for exams that have marks but no pivot data
     */
    private function populateStudentExamData(): void
    {
        $examQuery = Exam::where('sinf_id', $this->sinfId)
            ->where('subject_id', $this->subjectId);

        if ($this->quarter) {
            $examQuery->where('quarter', $this->quarter);
        } else {
            $examQuery->whereNotNull('quarter');
        }

        $exams = $examQuery->whereHas('marks')->get();

        foreach ($exams as $exam) {
            // Clear existing potentially wrong pivot data
            DB::table('student_exams')->where('exam_id', $exam->id)->delete();

            // Recalculate with the fixed logic
            $exam->calculateStudentTotals();
        }
    }

    /**
     * Get exam results for a student by type (BSB or CHSB) with quarter filtering
     */
    private function getExamResults($studentId, $examType)
    {
        $query = DB::table('student_exams')
            ->join('exams', 'student_exams.exam_id', '=', 'exams.id')
            ->where('student_exams.student_id', $studentId)
            ->where('exams.subject_id', $this->subjectId)
            ->where('exams.sinf_id', $this->sinfId)
            ->where('exams.type', $examType);

        // Apply quarter filter instead of date filters
        if ($this->quarter) {
            $query->where('exams.quarter', $this->quarter);
        } else {
            $query->whereNotNull('exams.quarter');
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
            'percentage_sum' => $percentageSum,
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
     * Filter by specific quarter
     */
    public function filterByQuarter($quarter): void
    {
        $this->quarter = $quarter;
        $this->applyFilters();
    }

    /**
     * Clear quarter filter to show all quarters
     */
    public function clearQuarterFilter(): void
    {
        $this->quarter = null;
        $this->applyFilters();
    }

    /**
     * Get available quarters for the current sinf and subject
     */
    public function getAvailableQuarters(): array
    {
        if (!$this->sinfId || !$this->subjectId) {
            return [];
        }

        return QuarterStatisticsService::getAvailableQuarters($this->sinfId, $this->subjectId);
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
            'quarter' => $this->quarter,
            'summary' => $this->summary,
            'generatedAt' => Carbon::now()->format('d.m.Y H:i'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.statistics-table', $pdfData)
            ->setPaper('a4', 'landscape');

        // Create filename
        $quarterText = $this->quarter ? "_{$this->quarter}_chorak" : "_barcha_choraklar";
        $filename = "{$sinf->name}_sinf_{$subject->name}_statistika{$quarterText}_" . date('Y-m-d') . ".pdf";

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
