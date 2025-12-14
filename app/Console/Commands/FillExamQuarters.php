<?php

namespace App\Console\Commands;

use App\Models\Exam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FillExamQuarters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exams:fill-quarters {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill exam quarters automatically based on serial_number and type (BSB/CHSB)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        } else {
            $this->info('📝 LIVE MODE - Changes will be applied');
        }

        $this->info('Starting quarter assignment process...');

        // Get all exams that need quarter updates
        $exams = Exam::whereIn('type', ['BSB', 'CHSB'])->get();

        $bsbUpdates = 0;
        $chsbUpdates = 0;
        $skipped = 0;

        $this->info("Found {$exams->count()} exams to process");

        foreach ($exams as $exam) {
            $newQuarter = $this->determineQuarter($exam->type, $exam->serial_number);

            if ($newQuarter === null) {
                $this->warn("⚠️  Skipping exam ID {$exam->id}: Invalid serial_number {$exam->serial_number} for type {$exam->type}");
                $skipped++;
                continue;
            }

            if ($exam->quarter === $newQuarter) {
                $this->line("✅ Exam ID {$exam->id} ({$exam->type}) already has correct quarter: {$newQuarter}");
                continue;
            }

            if (!$dryRun) {
                $exam->update(['quarter' => $newQuarter]);
            }

            $this->info("🔄 Updated exam ID {$exam->id} ({$exam->type}, serial: {$exam->serial_number}) from '{$exam->quarter}' to '{$newQuarter}'");

            if ($exam->type === 'BSB') {
                $bsbUpdates++;
            } else {
                $chsbUpdates++;
            }
        }

        $this->newLine();
        $this->info('📊 SUMMARY:');
        $this->table(
            ['Type', 'Updates', 'Logic'],
            [
                ['BSB', $bsbUpdates, 'Serial 1,2 → I | Serial 3,4 → II'],
                ['CHSB', $chsbUpdates, 'Serial 1 → I | Serial 2 → II'],
                ['Skipped', $skipped, 'Invalid serial numbers']
            ]
        );

        if ($dryRun) {
            $this->info('🔍 This was a dry run. To apply changes, run without --dry-run option');
        } else {
            $this->info('✅ Quarter assignment completed successfully!');
        }

        return 0;
    }

    /**
     * Determine the quarter based on exam type and serial number
     */
    private function determineQuarter(string $type, int $serialNumber): ?string
    {
        if ($type === 'BSB') {
            return match ($serialNumber) {
                1, 2 => 'I',
                3, 4 => 'II',
                default => null
            };
        }

        if ($type === 'CHSB') {
            return match ($serialNumber) {
                1 => 'I',
                2 => 'II',
                default => null
            };
        }

        return null;
    }
}
