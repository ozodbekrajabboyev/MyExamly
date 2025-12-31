<?php

namespace App\Console\Commands;

use App\Models\FbMark;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateFbMarks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fbmarks:populate
                           {--dry-run : Show what would be created without making changes}
                           {--quarter=* : Specific quarters to populate (I, II, III, IV). If not specified, all quarters will be processed}
                           {--batch-size=1000 : Number of records to process in each batch}
                           {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate fb_marks table with default values for all student-subject-quarter combinations';

    /**
     * Available quarters
     *
     * @var array
     */
    private array $availableQuarters = ['I', 'II', 'III', 'IV'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $quarters = $this->option('quarter') ?: $this->availableQuarters;
        $batchSize = (int) $this->option('batch-size');
        $force = $this->option('force');

        // Validate quarters
        $invalidQuarters = array_diff($quarters, $this->availableQuarters);
        if (!empty($invalidQuarters)) {
            $this->error('Invalid quarters: ' . implode(', ', $invalidQuarters));
            $this->info('Valid quarters are: ' . implode(', ', $this->availableQuarters));
            return 1;
        }

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        } else {
            $this->info('📝 LIVE MODE - Changes will be applied');
        }

        $this->info('Starting FB marks population process...');
        $this->info('Quarters to process: ' . implode(', ', $quarters));
        $this->info('Batch size: ' . $batchSize);

        // Get data counts
        $studentsCount = Student::count();
        $subjectsCount = Subject::count();
        $totalCombinations = $studentsCount * $subjectsCount * count($quarters);

        $this->newLine();
        $this->info('📊 DATA OVERVIEW:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Students', number_format($studentsCount)],
                ['Subjects', number_format($subjectsCount)],
                ['Quarters', count($quarters)],
                ['Total combinations', number_format($totalCombinations)],
            ]
        );

        // Check existing records
        $existingCount = FbMark::whereIn('quarter', $quarters)->count();
        if ($existingCount > 0) {
            $this->warn("⚠️  Found {$existingCount} existing FB marks for selected quarters");
            $this->info('These will be skipped to prevent duplicates');
        }

        // Confirmation
        if (!$dryRun && !$force) {
            if (!$this->confirm("Do you want to proceed with creating up to {$totalCombinations} FB mark records?")) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->newLine();

        // Process each quarter
        $totalCreated = 0;
        $totalSkipped = 0;

        foreach ($quarters as $quarter) {
            $this->info("🔄 Processing Quarter {$quarter}...");

            $result = $this->processQuarter($quarter, $batchSize, $dryRun);
            $totalCreated += $result['created'];
            $totalSkipped += $result['skipped'];
        }

        // Final summary
        $this->newLine();
        $this->info('📊 FINAL SUMMARY:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total FB marks created', number_format($totalCreated)],
                ['Total combinations skipped', number_format($totalSkipped)],
                ['Processing quarters', implode(', ', $quarters)],
                ['Default FB value used', '0']
            ]
        );

        if ($dryRun) {
            $this->info('🔍 This was a dry run. To apply changes, run without --dry-run option');
        } else {
            $this->info('✅ FB marks population completed successfully!');
        }

        return 0;
    }

    /**
     * Process a single quarter
     */
    private function processQuarter(string $quarter, int $batchSize, bool $dryRun): array
    {
        $created = 0;
        $skipped = 0;

        // Get all students with their sinf_id
        $students = Student::select('id', 'sinf_id')->get();
        $subjects = Subject::select('id')->get();

        // Get existing combinations for this quarter to avoid duplicates
        $existing = FbMark::where('quarter', $quarter)
            ->get(['student_id', 'subject_id'])
            ->map(fn($record) => "{$record->student_id}-{$record->subject_id}")
            ->toArray();

        $existingSet = array_flip($existing);

        $this->info("Found " . count($existing) . " existing records for quarter {$quarter}");

        // Prepare data for batch insert
        $batchData = [];
        $progressBar = $this->output->createProgressBar($students->count() * $subjects->count());
        $progressBar->setFormat('verbose');

        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                $key = "{$student->id}-{$subject->id}";

                if (isset($existingSet[$key])) {
                    $skipped++;
                } else {
                    $batchData[] = [
                        'quarter' => $quarter,
                        'sinf_id' => $student->sinf_id,
                        'subject_id' => $subject->id,
                        'student_id' => $student->id,
                        'fb' => 0, // Default value
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Process batch when it reaches the specified size
                    if (count($batchData) >= $batchSize) {
                        if (!$dryRun) {
                            DB::table('fb_marks')->insert($batchData);
                        }
                        $created += count($batchData);
                        $batchData = [];
                    }
                }

                $progressBar->advance();
            }
        }

        // Insert remaining records
        if (!empty($batchData)) {
            if (!$dryRun) {
                DB::table('fb_marks')->insert($batchData);
            }
            $created += count($batchData);
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("✅ Quarter {$quarter}: Created {$created}, Skipped {$skipped}");

        return ['created' => $created, 'skipped' => $skipped];
    }
}
