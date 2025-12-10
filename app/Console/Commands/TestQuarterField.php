<?php

namespace App\Console\Commands;

use App\Models\Exam;
use Illuminate\Console\Command;

class TestQuarterField extends Command
{
    protected $signature = 'test:quarter-field';
    protected $description = 'Test the quarter field functionality';

    public function handle()
    {
        $this->info('Testing quarter field functionality...');

        // Get an existing exam to base our test on
        $baseExam = Exam::first();
        if (!$baseExam) {
            $this->error('No exams found in database');
            return 1;
        }

        try {
            // Find a combination that doesn't exist yet
            $maxSerial = Exam::where('sinf_id', $baseExam->sinf_id)
                ->where('subject_id', $baseExam->subject_id)
                ->max('serial_number');

            $newSerial = $maxSerial + 1;

            $testExam = new Exam();
            $testExam->sinf_id = $baseExam->sinf_id;
            $testExam->subject_id = $baseExam->subject_id;
            $testExam->serial_number = $newSerial;
            $testExam->maktab_id = $baseExam->maktab_id;
            $testExam->teacher_id = $baseExam->teacher_id;
            $testExam->type = 'BSB';
            $testExam->metod_id = $baseExam->metod_id;
            $testExam->quarter = 'II'; // Test the quarter field

            $testExam->save();
            $this->info('✅ SUCCESS: Exam created with quarter field!');
            $this->line("Created exam with serial {$newSerial} for quarter II");

            // Test creating another exam with same serial/subject/sinf but different quarter (should fail)
            $this->line('Testing that quarter does NOT affect uniqueness...');

            $duplicateExam = new Exam();
            $duplicateExam->sinf_id = $baseExam->sinf_id;
            $duplicateExam->subject_id = $baseExam->subject_id;
            $duplicateExam->serial_number = $newSerial; // Same serial
            $duplicateExam->maktab_id = $baseExam->maktab_id;
            $duplicateExam->teacher_id = $baseExam->teacher_id;
            $duplicateExam->type = 'CHSB';
            $duplicateExam->metod_id = $baseExam->metod_id;
            $duplicateExam->quarter = 'III'; // Different quarter, but should still fail

            try {
                $duplicateExam->save();
                $this->error('❌ FAILURE: Duplicate serial was allowed even with different quarter!');
                $duplicateExam->delete();
            } catch (\Exception $e) {
                $this->info('✅ SUCCESS: Quarter does not affect uniqueness constraint (as intended)');
            }

            // Clean up
            $testExam->delete();
            $this->info('Test exam cleaned up');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ FAILURE: Could not create exam with quarter field');
            $this->line('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
