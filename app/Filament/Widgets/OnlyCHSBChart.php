<?php

namespace App\Filament\Widgets;

use App\Models\Exam;
use App\Models\Mark;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On; // Bu import qo'shildi

class OnlyCHSBChart extends ChartWidget
{
    protected static ?string $heading = 'CHSB Chart'; // Nom o'zgartirildi

    public ?int $sinfId = null;
    public ?int $subjectId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        $this->startDate = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    /**
     * Bu event listener qo'shildi - eng muhim qism!
     * Filtr o'zgarganda bu metod chaqiriladi
     */
    #[On('updateStats')]
    public function updateStats(?int $sinfId, ?int $subjectId, ?string $startDate, ?string $endDate): void
    {
        $this->sinfId = $sinfId;
        $this->subjectId = $subjectId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

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
            ->where('subject_id', $this->subjectId)
            ->where('type', 'CHSB')
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
                    'label' => "O'zlashtirish foizi (%)",
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
