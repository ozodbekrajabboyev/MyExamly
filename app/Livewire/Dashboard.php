<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Mark;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $exam = Exam::find(2)->id;
        $marks = Mark::where('exam_id', $exam);

        dd($marks->count());


        $problems = Mark::where('exam_id', $exam)->get();

//        dd($students);

        return view('livewire.dashboard', [
            'marks' => $marks,
            'student'
        ] );
    }

//    public function marks()
//    {
//
//    }
}
