<?php

namespace App\Console\Commands;

use App\Models\Exam;
use Illuminate\Console\Command;

class GenerateExamCodes extends Command
{
    protected $signature = 'exams:generate-codes';
    protected $description = 'Generate unique 5-digit codes for existing exams without a code';

    public function handle()
    {
        $this->info("Starting to generate unique codes...");

        $exams = Exam::whereNull('code')->orWhere('code', '')->get();

        if ($exams->isEmpty()) {
            $this->info("All exams already have unique codes.");
            return Command::SUCCESS;
        }

        foreach ($exams as $exam) {
            $exam->code = $this->generateUniqueCode();
            $exam->save();

            $this->info("✔ Code {$exam->code} added for exam ID {$exam->id}");
        }

        $this->info("All missing codes generated successfully.");

        return Command::SUCCESS;
    }

    private function generateUniqueCode()
    {
        do {
            $code = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (Exam::where('code', $code)->exists());

        return $code;
    }
}
