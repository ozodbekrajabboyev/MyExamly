<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function byClass($classId)
    {
        return Student::where('sinf_id', $classId)
            ->select('id', 'full_name')
            ->orderBy('full_name')
            ->get();
    }


    public function result($studentId)
    {
        $student = Student::with(['sinf.maktab'])->findOrFail($studentId);

        $marks = Mark::with(['exam.subject'])
            ->where('student_id', $studentId)
            ->get()
            ->groupBy(fn($mark) => $mark->exam->subject->name ?? 'Noma’lum fan')
            ->map(function ($subjectMarks, $subjectName) {
                $exams = $subjectMarks->groupBy(fn($m) => $m->exam->type . '-' . $m->exam->serial_number);
                $subjectResults = [];

                foreach ($exams as $examKey => $examMarks) {
                    $examType = $examMarks->first()->exam->type;
                    $serial = $examMarks->first()->exam->serial_number;
                    $problems = collect($examMarks->first()->exam->problems ?? []);

                    $taskResults = [];
                    $totalScore = 0;
                    $totalMax = 0;

                    foreach ($examMarks as $i => $mark) {
                        $problem = $problems->firstWhere('id', $mark->problem_id);
                        $maxMark = $problem['max_mark'] ?? 0;
                        $percent = $maxMark > 0 ? round(($mark->mark / $maxMark) * 100, 0) : 0;

                        $taskResults[] = [
                            'number' => $i + 1,
                            'score' => $mark->mark,
                            'max' => $maxMark,
                            'percent' => $percent
                        ];

                        $totalScore += $mark->mark;
                        $totalMax += $maxMark;
                    }

                    $overall = $totalMax > 0 ? round(($totalScore / $totalMax) * 100, 0) : 0;

                    $subjectResults[] = [
                        'exam_type' => $examType,
                        'serial_number' => $serial,
                        'tasks' => $taskResults,
                        'overall' => $overall
                    ];
                }

                return [
                    'subject' => $subjectName,
                    'exams' => $subjectResults
                ];
            })
            ->values();

        // 🧩 Telegram yoki Postman formatida chiroyli text tayyorlash
        $output = [];
        $output[] = "📋 O‘quvchi: " . $student->full_name;
        $output[] = "🏫 Maktab: " . ($student->sinf->maktab->name ?? 'Noma’lum maktab');
        $output[] = "";

        foreach ($marks as $subject) {
            foreach ($subject['exams'] as $exam) {
                $output[] = "📘 Fan: " . $subject['subject'];
                $output[] = "   🧾 Imtihon turi: " . $exam['serial_number'] . "-" . strtoupper($exam['exam_type']);
                $output[] = "";

                foreach ($exam['tasks'] as $task) {
                    $output[] = sprintf(
                        "%d-topshiriq: %d ball / %d balldan (%d%%)",
                        $task['number'],
                        $task['score'],
                        $task['max'],
                        $task['percent']
                    );
                }

                $output[] = "";
                $output[] = "📈 Umumiy natija: {$exam['overall']}%";
                $output[] = "\n------------------------------------------------------------\n";
            }
        }

        return response()->json([
//            'student' => [
//                'full_name' => $student->full_name,
//                'school' => $student->sinf->maktab->name ?? null,
//            ],
//            'subjects' => $marks,
            'formatted' => implode("\n", $output)
        ]);
    }



}
