<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Problem;
use App\Models\Student;
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
