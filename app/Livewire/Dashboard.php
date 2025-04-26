<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Problem;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $exam = Exam::find(2)->id;
        $marks = Mark::where('exam_id', $exam)->get();

        $problems = Problem::where('exam_id', $exam)->get();

        return view('livewire.dashboard', [
            'marks' => $marks,
            'marks_count' => $marks->count(),
            'problems' => $problems,
        ] );
    }

//    public function marks()
//    {
//
//    }
}
