<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneratePdfController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('pdf.generate_pdf');
    }

    public function downloadByCode($code)
    {
        // Find exam by code - adjust this based on your exam model structure
        $exam = Exam::where('code', $code)
                   ->orWhere('id', $code)
                   ->orWhere('serial_number', $code)
                   ->with(['sinf', 'subject'])
                   ->first();

        if (!$exam) {
            // If exam not found, redirect back with error
            return redirect('/generate_pdf')->with('error', 'Imtihon kodi topilmadi');
        }

        // Fetch the necessary data for the PDF
        $marks = \App\Models\Mark::where('exam_id', $exam->id)->get();

        // Fix: Handle problems as JSON array (same as your working method)
        $problemsData = is_string($exam->problems) ? json_decode($exam->problems, true) : ($exam->problems ?? []);
        $problems = collect($problemsData)->sortBy('id')->values();

        // Load students with their pre-calculated pivot data (same as your working method)
        $students = \App\Models\Student::where('sinf_id', $exam->sinf_id)
            ->with(['exams' => function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            }])
            ->orderBy('full_name')
            ->get();

        $totalMaxScore = $problems->sum('max_mark');

        // Generate PDF using dashboard-table.blade.php
        $pdf = Pdf::loadView('pdf.dashboard-table', [
            'exam' => $exam,
            'students' => $students,
            'problems' => $problems,
            'totalMaxScore' => $totalMaxScore,
            'marks' => $marks,
        ])->setPaper('a4', 'landscape');

        // Generate filename (same format as your working method)
        $className = $exam->sinf->name;
        $subject = $exam->subject->name;
        $type1 = $exam->serial_number . "-" . $exam->type;
        $filename = "$className -sinf | $subject | $type1 | results.pdf";

        // Return the response to download the file in the browser
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}
