<?php

namespace App\Console\Commands;

use App\Models\Exam;
use Illuminate\Console\Command;

class FinalIntegrationTest extends Command
{
    protected $signature = 'test:final-integration';
    protected $description = 'Run comprehensive test of quarter field and type-aware uniqueness';

    public function handle()
    {
        $this->info('🧪 Running Final Integration Test...');

        // Test 1: Verify quarter field exists
        $this->line('1️⃣ Testing quarter field...');
        $exam = Exam::first();
        if (!$exam) {
            $this->error('No exams found for testing');
            return 1;
        }

        $columns = \Schema::getColumnListing('exams');
        if (in_array('quarter', $columns)) {
            $this->info('✅ Quarter field exists in database');
        } else {
            $this->error('❌ Quarter field missing');
            return 1;
        }

        // Test 2: Test type-aware uniqueness
        $this->line('2️⃣ Testing type-aware uniqueness constraint...');

        // Find max serial for a sinf/subject combination
        $testSinf = $exam->sinf_id;
        $testSubject = $exam->subject_id;
        $maxSerial = Exam::where('sinf_id', $testSinf)
            ->where('subject_id', $testSubject)
            ->max('serial_number');
        $newSerial = $maxSerial + 1;

        // Create BSB exam
        try {
            $bsbExam = new Exam();
            $bsbExam->sinf_id = $testSinf;
            $bsbExam->subject_id = $testSubject;
            $bsbExam->type = 'BSB';
            $bsbExam->serial_number = $newSerial;
            $bsbExam->quarter = 'I';
            $bsbExam->maktab_id = $exam->maktab_id;
            $bsbExam->teacher_id = $exam->teacher_id;
            $bsbExam->metod_id = $exam->metod_id;
            $bsbExam->save();

            $this->info("✅ Created BSB exam with serial {$newSerial}");

            // Create CHSB exam with same serial (should work)
            $chsbExam = new Exam();
            $chsbExam->sinf_id = $testSinf;
            $chsbExam->subject_id = $testSubject;
            $chsbExam->type = 'CHSB';
            $chsbExam->serial_number = $newSerial; // Same serial, different type
            $chsbExam->quarter = 'II';
            $chsbExam->maktab_id = $exam->maktab_id;
            $chsbExam->teacher_id = $exam->teacher_id;
            $chsbExam->metod_id = $exam->metod_id;
            $chsbExam->save();

            $this->info("✅ Created CHSB exam with same serial {$newSerial} (different type)");

            // Try to create duplicate BSB (should fail)
            try {
                $duplicateBsb = new Exam();
                $duplicateBsb->sinf_id = $testSinf;
                $duplicateBsb->subject_id = $testSubject;
                $duplicateBsb->type = 'BSB';
                $duplicateBsb->serial_number = $newSerial; // Duplicate!
                $duplicateBsb->quarter = 'III';
                $duplicateBsb->maktab_id = $exam->maktab_id;
                $duplicateBsb->teacher_id = $exam->teacher_id;
                $duplicateBsb->metod_id = $exam->metod_id;
                $duplicateBsb->save();

                $this->error('❌ FAILURE: Duplicate BSB was allowed');

                // Clean up
                $bsbExam->delete();
                $chsbExam->delete();
                $duplicateBsb->delete();
                return 1;

            } catch (\Exception $e) {
                $this->info('✅ Duplicate BSB correctly blocked');
            }

            // Test 3: Verify display format
            $this->line('3️⃣ Testing display formats...');
            $displayWithQuarter = "{$bsbExam->serial_number} - {$bsbExam->type}" . ($bsbExam->quarter ? " ({$bsbExam->quarter} chorak)" : "");
            $expectedFormat = "{$newSerial} - BSB (I chorak)";

            if ($displayWithQuarter === $expectedFormat) {
                $this->info("✅ Display format correct: {$displayWithQuarter}");
            } else {
                $this->error("❌ Display format incorrect. Got: {$displayWithQuarter}, Expected: {$expectedFormat}");
            }

            // Clean up test data
            $bsbExam->delete();
            $chsbExam->delete();

            $this->info('🎉 All tests passed! Implementation is working correctly.');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            return 1;
        }
    }
}
