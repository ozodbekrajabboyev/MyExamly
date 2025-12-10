<?php

namespace App\Console\Commands;

use App\Models\Exam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResolveDuplicateSerialNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exams:resolve-duplicates {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resolve duplicate serial numbers in exams table by renumbering them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('Resolving duplicate serial numbers in exams table...');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Get all duplicates - now considering type as well
        $duplicateGroups = DB::table('exams')
            ->select('sinf_id', 'subject_id', 'type', 'serial_number', DB::raw('array_agg(id) as exam_ids'))
            ->groupBy('sinf_id', 'subject_id', 'type', 'serial_number')
            ->havingRaw('count(*) > 1')
            ->get();

        if ($duplicateGroups->count() === 0) {
            $this->info('No duplicate serial numbers found!');
            return 0;
        }

        $this->info("Found {$duplicateGroups->count()} groups of duplicate serial numbers");

        $totalFixed = 0;

        foreach ($duplicateGroups as $group) {
            $examIds = str_getcsv(trim($group->exam_ids, '{}'));

            $this->line("Processing Sinf ID: {$group->sinf_id}, Subject ID: {$group->subject_id}, Type: {$group->type}, Serial: {$group->serial_number}");
            $this->line("  Found " . count($examIds) . " duplicates with IDs: " . implode(', ', $examIds));

            // Get the maximum serial number for this sinf/subject/type combination
            $maxSerial = DB::table('exams')
                ->where('sinf_id', $group->sinf_id)
                ->where('subject_id', $group->subject_id)
                ->where('type', $group->type)
                ->max('serial_number');

            // Skip the first exam (keep its original serial number)
            $examsToUpdate = array_slice($examIds, 1);

            foreach ($examsToUpdate as $index => $examId) {
                $newSerial = $maxSerial + $index + 1;

                if ($dryRun) {
                    $this->line("  Would update exam ID {$examId} to serial number {$newSerial}");
                } else {
                    DB::table('exams')
                        ->where('id', $examId)
                        ->update(['serial_number' => $newSerial]);

                    $this->line("  Updated exam ID {$examId} to serial number {$newSerial}");
                }

                $totalFixed++;
            }
        }

        if ($dryRun) {
            $this->info("Would fix {$totalFixed} duplicate serial numbers");
            $this->info("Run without --dry-run to apply changes");
        } else {
            $this->info("Fixed {$totalFixed} duplicate serial numbers");
            $this->info("You can now run the migration to add the unique constraint");
        }

        return 0;
    }
}
