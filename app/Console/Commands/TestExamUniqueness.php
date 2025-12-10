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

        $this->line("Testing with existing exam: Sinf {$exam->sinf_id}, Subject {$exam->subject_id}, Type {$exam->type}, Serial {$exam->serial_number}");

        try {
            $testExam = new Exam();
            $testExam->sinf_id = $exam->sinf_id;
            $testExam->subject_id = $exam->subject_id;
            $testExam->type = $exam->type; // Same type
            $testExam->serial_number = $exam->serial_number; // Same serial number
            $testExam->maktab_id = $exam->maktab_id;
            $testExam->teacher_id = $exam->teacher_id;
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

            // Now test that different type is allowed
            $this->line('Testing that different type IS allowed...');
            try {
                $differentTypeExam = new Exam();
                $differentTypeExam->sinf_id = $exam->sinf_id;
                $differentTypeExam->subject_id = $exam->subject_id;
                $differentTypeExam->type = ($exam->type === 'BSB') ? 'CHSB' : 'BSB'; // Different type
                $differentTypeExam->serial_number = $exam->serial_number; // Same serial number
                $differentTypeExam->maktab_id = $exam->maktab_id;
                $differentTypeExam->teacher_id = $exam->teacher_id;
                $differentTypeExam->metod_id = $exam->metod_id;
                $differentTypeExam->quarter = 'II';

                $differentTypeExam->save();
                $this->info('✅ SUCCESS: Different type with same serial number was allowed!');

                // Clean up
                $differentTypeExam->delete();

            } catch (\Exception $e2) {
                $this->error('❌ FAILURE: Different type was not allowed - this should be permitted!');
                $this->line('Error: ' . $e2->getMessage());
                return 1;
            }

            return 0;
        }
    }
}
