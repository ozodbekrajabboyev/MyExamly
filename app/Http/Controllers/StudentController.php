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

        $marks = Mark::with(['exam.subject'])
            ->where('student_id', $studentId)
            ->whereHas('exam', fn($q) => $q->where('subject_id', $subjectId))
            ->get()
            ->groupBy(fn($m) => $m->exam->type . '-' . $m->exam->serial_number);

        $subjectName = $marks->first()?->first()?->exam->subject->name ?? 'Noma’lum fan';
        $examResults = [];

        foreach ($marks as $examKey => $examMarks) {
            $exam = $examMarks->first()->exam;
            $problems = collect($exam->problems ?? []);

            $taskResults = [];
            $totalScore = 0;
            $totalMax = 0;

            foreach ($examMarks as $i => $mark) {
                $problem = $problems->firstWhere('id', $mark->problem_id);
                $maxMark = $problem['max_mark'] ?? 0;
                $percent = $maxMark > 0 ? round(($mark->mark / $maxMark) * 100) : 0;

                $taskResults[] = sprintf(
                    "%d-topshiriq: %d ball / %d balldan (%d%%)",
                    $i + 1,
                    $mark->mark,
                    $maxMark,
                    $percent
                );

                $totalScore += $mark->mark;
                $totalMax += $maxMark;
            }

            $overall = $totalMax > 0 ? round(($totalScore / $totalMax) * 100) : 0;

            $examResults[] = [
                'exam_type' => $exam->type,
                'serial_number' => $exam->serial_number,
                'tasks' => $taskResults,
                'overall' => $overall,
            ];
        }

        $formatted = [];
        $formatted[] = "📋 O‘quvchi: " . $student->full_name;
        $formatted[] = "🏫 Maktab: " . ($student->sinf->maktab->name ?? 'Noma’lum maktab');
        $formatted[] = "📘 Fan: {$subjectName}";
        $formatted[] = "";

        foreach ($examResults as $exam) {
            $formatted[] = "🧾 Imtihon turi: {$exam['serial_number']}-{$exam['exam_type']}";
            $formatted = array_merge($formatted, $exam['tasks']);
            $formatted[] = "";
            $formatted[] = "📈 Umumiy natija: {$exam['overall']}%";
            $formatted[] = "";
            $formatted[] = "------------------------------------------------------------";
        }

        return response()->json([
            'student' => [
                'full_name' => $student->full_name,
                'school' => $student->sinf->maktab->name ?? null,
            ],
            'subject' => $subjectName,
            'exams' => $examResults,
            'formatted' => implode("\n", $formatted),
        ]);
    }



}
