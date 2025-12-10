<?php

namespace App\Console\Commands;

use App\Models\Exam;
use Illuminate\Console\Command;

class TestExamUniqueness extends Command
{
    protected $signature = 'test:exam-uniqueness';
    protected $description = 'Test the exam uniqueness constraint';

    public function handle()
    {
        $this->info('Testing exam uniqueness constraint...');

        // Get first exam
        $exam = Exam::first();
        if (!$exam) {
            $this->error('No exams found in database');
            return 1;
        }

        $this->line("Testing with existing exam: Sinf {$exam->sinf_id}, Subject {$exam->subject_id}, Serial {$exam->serial_number}");

        try {
            $testExam = new Exam();
            $testExam->sinf_id = $exam->sinf_id;
            $testExam->subject_id = $exam->subject_id;
            $testExam->serial_number = $exam->serial_number; // Same serial number
            $testExam->maktab_id = $exam->maktab_id;
            $testExam->teacher_id = $exam->teacher_id;
            $testExam->type = $exam->type;
            $testExam->metod_id = $exam->metod_id;
            $testExam->quarter = 'I'; // Different quarter, but constraint should still work

            $testExam->save();
            $this->error('❌ FAILURE: Duplicate was allowed - constraint is NOT working!');

            // Clean up the test exam
            $testExam->delete();
            return 1;

        } catch (\Exception $e) {
            $this->info('✅ SUCCESS: Uniqueness constraint is working!');
            $this->line('Error message: ' . $e->getMessage());
            return 0;
        }
    }
}
