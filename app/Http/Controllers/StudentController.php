<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function byClass($classId)
    {
        return Student::where('sinf_id', $classId)
            ->select('id', 'full_name')
            ->orderBy('full_name')
            ->get();
    }

    public function subjects($studentId)
    {
        $subjects = Mark::with('exam.subject')
            ->where('student_id', $studentId)
            ->get()
            ->pluck('exam.subject')
            ->unique('id')
            ->map(fn($subject) => [
                'id' => $subject->id,
                'name' => $subject->name,
            ])
            ->values();

        return response()->json([
            'student_id' => (int)$studentId,
            'subjects' => $subjects,
        ]);
    }


    public function result($studentId, $subjectId)
    {
        $student = Student::with(['sinf.maktab'])->findOrFail($studentId);

        $results = DB::table('student_exams')
            ->join('exams', 'student_exams.exam_id', '=', 'exams.id')
            ->join('subjects', 'exams.subject_id', '=', 'subjects.id')
            ->where('student_exams.student_id', $studentId)
            ->where('exams.subject_id', $subjectId)
            ->select(
                'student_exams.total',
                'student_exams.percentage',
                'exams.type',
                'exams.serial_number',
                'subjects.name as subject_name'
            )
            ->get();

        if ($results->isEmpty()) {
            return response()->json(["message" => "Natija topilmadi"], 404);
        }

        $subjectName = $results->first()->subject_name;

        // 1) collection sifatida exam results
        $examResults = $results->map(function ($item) {
            return [
                'exam_type'    => $item->type,
                'serial_number'=> $item->serial_number,
                'total'        => (float) $item->total,
                'percentage'   => round($item->percentage, 0),
            ];
        });

        // 2) Sort — BSB oldin, CHSB keyin
        $examResults = $examResults
            ->sortBy([
                ['exam_type', fn($a, $b) => $a === $b ? 0 : ($a === 'BSB' ? -1 : 1)],
                ['serial_number', 'asc']
            ])
            ->values();

        // 3) Group by exam type (BSB, CHSB)
        $grouped = $examResults
            ->groupBy('exam_type')
            ->map(function($items) {
                return $items->map(function($item) {
                    return [
                        'serial_number' => $item['serial_number'],
                        'percentage' => $item['percentage'],
                        'total' => $item['total']
                    ];
                })->values();
            });

        // 4) Telegram uchun formatted text
        $formatted = [];
        $formatted[] = "📋 O‘quvchi: " . $student->full_name;
        $formatted[] = "🏫 Maktab: " . ($student->sinf->maktab->name ?? 'Noma’lum maktab');
        $formatted[] = "📘 Fan: {$subjectName}\n";

        foreach ($examResults as $exam) {
            $formatted[] = "🧾 Imtihon turi: {$exam['serial_number']}-{$exam['exam_type']}";
            $formatted[] = "💯 Jami ball: " . $exam['total'];
            $formatted[] = "📈 Umumiy natija: " . $exam['percentage'] . "%";
            $formatted[] = "------------------------------------------------------------";
        }

        return response()->json([
            "student" => $student->full_name,
            'class_name' => $student->sinf->name,
            "school" => $student->sinf->maktab->name,
            "subject" => $subjectName,
            "groups" => $grouped,   // chart uchun muhim!
            "formatted" => implode("\n", $formatted)
        ]);
    }

}
