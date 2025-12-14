<?php

namespace App\Filament\Widgets;

use App\Models\Exam;
use App\Models\Mark;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class StatisticsChartWidgetByBSB extends ChartWidget
{
    /**
     * The heading to be displayed on the chart widget.
     *
     * @var string
     */
    protected static ?string $heading = 'BSB imtihonlar grafiki';

    public static function canView(): bool
    {
        return request()->routeIs('filament.app.pages.statistics');
    }

    /**
     * The polling interval for the chart. Null means no polling.
     *
     * @var string|null
     */
    protected static ?string $pollingInterval = null;

    /**
     * Properties to store the current filter state.
     */
    public ?int $sinfId = null;
    public ?int $subjectId = null;
    public ?string $quarter = null;

    /**
     * The mount method sets the initial state when the widget loads.
     */
    public function mount(): void
    {
        // No default initialization needed for quarter-based filtering
    }

    /**
     * This method listens for the 'updateStats' event dispatched by our filter component.
     * The #[On] attribute is Livewire 3's modern syntax for event listeners.
     * When the event is received, it updates the widget's public properties.
     * Livewire will automatically re-render the component, calling getData() again.
     *
     * @param int|null $sinfId
     * @param int|null $subjectId
     * @param string|null $quarter
     * @return void
     */
    #[On('updateStats')]
    public function updateStats(?int $sinfId, ?int $subjectId, ?string $quarter): void
    {
        $this->sinfId = $sinfId;
        $this->subjectId = $subjectId;
        $this->quarter = $quarter;
    }

    /**
     * This is the core data retrieval method for the chart.
     * It builds a query based on the current filter properties.
     *
     * @return array
     */
    protected function getData(): array
    {
        if (!$this->sinfId || !$this->subjectId) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $examQuery = Exam::query()
            ->where('maktab_id', auth()->user()->maktab_id)
            ->where('sinf_id', $this->sinfId)
            ->where('type', 'BSB')
            ->where('subject_id', $this->subjectId)
            ->whereHas('marks') // Only include exams that have marks
            ->whereNotNull('quarter'); // Exclude exams with null quarters

        // Apply quarter filter if specified
        if ($this->quarter) {
            $examQuery->where('quarter', $this->quarter);
        }

        $exams = $examQuery->orderBy('quarter')->orderBy('serial_number')->get();

        if ($exams->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $examIds = $exams->pluck('id');

        $maxMarksPerExam = DB::table('exams')
            ->whereIn('id', $examIds)
            ->select('id as exam_id',
                DB::raw("(SELECT SUM((value->>'max_mark')::numeric)
                  FROM jsonb_array_elements(problems) AS value) as total_max_mark")
            )
            ->get()
            ->keyBy('exam_id');
        // Calculate total max marks from the problems JSONB column
        $maxMarksPerExam = collect();
        foreach ($exams as $exam) {
            $problems = is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems;
            $totalMaxMark = 0;

            if (is_array($problems)) {
                foreach ($problems as $problem) {
                    if (isset($problem['max_mark'])) {
                        $totalMaxMark += (float) $problem['max_mark'];
                    }
                }
            }

            $maxMarksPerExam->put($exam->id, (object) ['total_max_mark' => $totalMaxMark]);
        }

        $studentMarksPerExam = Mark::query()
            ->whereIn('exam_id', $examIds)->groupBy('exam_id', 'student_id')
            ->select('exam_id', 'student_id', DB::raw('SUM(mark) as total_student_mark'))
            ->get()->groupBy('exam_id');

        $labels = [];
        $data = [];

        foreach ($exams as $exam) {
            $totalMaxScore = $maxMarksPerExam->get($exam->id)?->total_max_mark;
            if (!$totalMaxScore || $totalMaxScore == 0) continue;

            $marksForThisExam = $studentMarksPerExam->get($exam->id);
            if (!$marksForThisExam || $marksForThisExam->isEmpty()) continue;

            $averageScore = round($marksForThisExam->avg('total_student_mark'), 1);

            $masteryPercentage = round(($averageScore / $totalMaxScore) * 100, 1);

            $quarterText = $exam->quarter ? "({$exam->quarter} chorak)" : "";
            $labels[] = "{$exam->serial_number}-{$exam->type} {$quarterText}";
            $data[] = $masteryPercentage;
        }

        return [
            'datasets' => [
                [
                    'label' => "O'zlashtirish foizi (%)",
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Defines the type of chart to be rendered.
     *
     * @return string
     */
    protected function getType(): string
    {
        return 'line';
    }
}
