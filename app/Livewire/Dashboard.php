<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Problem;
use App\Models\Student;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $exam = Exam::find(1);
        $marks = Mark::where('exam_id', $exam->id)->get();

        $problems = Problem::where('exam_id', $exam->id)->get();
        $students = Student::where('sinf_id', $exam->sinf_id)->get();
        
        return view('livewire.dashboard', [
            'marks' => $marks,
            'marks_count' => $marks->count(),
            'problems' => $problems,
            'students' => $students
        ] );
    }

//    public function marks()
//    {
//
//    }
}
