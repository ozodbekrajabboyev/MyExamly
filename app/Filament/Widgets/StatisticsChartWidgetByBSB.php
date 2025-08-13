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
    public ?string $startDate = null;
    public ?string $endDate = null;

    /**
     * The mount method sets the initial state when the widget loads.
     * We'll set a default date range to match the filter component.
     * We also trigger an initial data load.
     */
    public function mount(): void
    {
        $this->startDate = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    /**
     * This method listens for the 'updateStats' event dispatched by our filter component.
     * The #[On] attribute is Livewire 3's modern syntax for event listeners.
     * When the event is received, it updates the widget's public properties.
     * Livewire will automatically re-render the component, calling getData() again.
     *
     * @param int|null $sinfId
     * @param int|null $subjectId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return void
     */
    #[On('updateStats')]
    public function updateStats(?int $sinfId, ?int $subjectId, ?string $startDate, ?string $endDate): void
    {
        $this->sinfId = $sinfId;
        $this->subjectId = $subjectId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * This is the core data retrieval method for the chart.
     * It builds a query based on the current filter properties.
     *
     * @return array
     */
    protected function getData(): array
    {
        if (!$this->sinfId || !$this->subjectId || !$this->startDate || !$this->endDate) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $exams = Exam::query()
            ->where('maktab_id', auth()->user()->maktab_id)
            ->where('sinf_id', $this->sinfId)
            ->where('type', 'BSB')
            ->where('subject_id', $this->subjectId)
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->whereHas('problems')
            ->orderBy('created_at')
            ->get();

        if ($exams->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $examIds = $exams->pluck('id');

        $maxMarksPerExam = DB::table('problems')
            ->whereIn('exam_id', $examIds)->groupBy('exam_id')
            ->select('exam_id', DB::raw('SUM(max_mark) as total_max_mark'))
            ->get()->keyBy('exam_id');

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

            $labels[] = "{$exam->serial_number}-{$exam->type} (" . Carbon::parse($exam->created_at)->format('M d') . ")";
            $data[] = $masteryPercentage;
        }

        return [
            'datasets' => [
                [
                    'label' => "O'zlashtirish foizi (%)", // Updated label to match
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
        return 'bar';
    }
}
