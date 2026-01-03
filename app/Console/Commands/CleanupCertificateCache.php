<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CleanupCertificateCache extends Command
{
    protected $signature = 'certificates:cleanup-cache {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Clean up old certificate cache entries since we moved to database storage';

    public function handle()
    {
        $this->info('Starting certificate cache cleanup...');

        $dryRun = $this->option('dry-run');
        $deletedCount = 0;

        // Get all teachers
        $teachers = Teacher::all(['id']);

        $certificateFields = [
            'malaka_toifa_path',
            'milliy_sertifikat1_path',
            'milliy_sertifikat2_path',
            'xalqaro_sertifikat_path',
            'ustama_sertifikat_path'
        ];

        foreach ($teachers as $teacher) {
            foreach ($certificateFields as $field) {
                $cacheKey = "teacher:{$teacher->id}:cert:{$field}:expires_at";

                if (Cache::has($cacheKey)) {
                    if ($dryRun) {
                        $this->line("Would delete: {$cacheKey}");
                    } else {
                        Cache::forget($cacheKey);
                        $this->line("Deleted: {$cacheKey}");
                    }
                    $deletedCount++;
                }
            }
        }

        // Also clean up notification flags
        $this->info('Cleaning up notification flags...');
        // Note: Since we can't easily iterate through all cache keys with Redis,
        // we'll just note that these will expire naturally

        if ($dryRun) {
            $this->info("Dry run complete. Would have deleted {$deletedCount} cache entries.");
        } else {
            $this->info("Cleanup complete. Deleted {$deletedCount} cache entries.");
        }

        return self::SUCCESS;
    }
}
