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
            return response()->json([
                "message" => "Natija topilmadi"
            ], 404);
        }

        $subjectName = $results->first()->subject_name;

        // Build exams data
        $examResults = $results->map(function ($item) {
            return [
                'exam_type'    => $item->type,
                'serial_number'=> $item->serial_number,
                'total'        => (float) $item->total,
                'percentage'   => round($item->percentage, 0),
            ];
        })->toArray();

        // Sort: BSB first, then CHSB, serial_number ASC
        usort($examResults, function ($a, $b) {
            if ($a['exam_type'] === $b['exam_type']) {
                return $a['serial_number'] <=> $b['serial_number'];
            }
            return $a['exam_type'] === 'BSB' ? -1 : 1;
        });

        // Telegram output
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

        // 👉 Correct final JSON
        return response()->json([
            'student' => $student->full_name,
            'school'  => $student->sinf->maktab->name ?? null,
            'subject' => $subjectName,
            'exams'   => $examResults,
            'formatted' => implode("\n", $formatted),
        ]);
    }

}
