<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Problem;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function downloadPdf()
    {
        $exam = Exam::with(['sinf', 'subject'])->find($this->selectedExamId);
        $marks = Mark::where('exam_id', $exam->id)->get();
        $problems = Problem::where('exam_id', $exam->id)->orderBy('problem_number')->get();
        $students = Student::where('sinf_id', $exam->sinf_id)->orderBy('full_name')->get();
        $totalMaxScore = $problems->sum('max_mark');

        $pdf = Pdf::loadView('pdf.dashboard-table', [
            'exam' => $exam,
            'students' => $this->students,
            'problems' => $this->problems,
            'totalMaxScore' => $this->totalMaxScore,
            'marks' => $this->marks, // << THIS is needed
        ])->setPaper('a4', 'landscape');

        $exam1 = Exam::find($this->selectedExamId);
        $className = $exam1->sinf->name;
        $subject = $exam1->subject->name;
        $type1 = $exam1->serial_number . "-" . $exam1->type;
        $filename1 = "$className -sinf | $subject | $type1 | results.pdf";
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename1);
    }

    public function render()
    {
        $exams = Exam::whereHas('problems.marks')
            ->with(['sinf', 'subject'])
            ->get();

        return view('livewire.dashboard', [
            'exams' => $exams,
        ]);
    }
}
