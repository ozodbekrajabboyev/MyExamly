<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CleanupSignatureFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:signature-files {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all signature files from storage after signature functionality removal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting signature files cleanup...');

        // Define signature directories
        $signatureDirectories = [
            'storage/app/public/signatures',
            'public/storage/signatures'
        ];

        $deletedFiles = 0;
        $deletedDirs = 0;

        foreach ($signatureDirectories as $directory) {
            $fullPath = base_path($directory);

            if (File::exists($fullPath)) {
                $this->info("📁 Found signature directory: {$directory}");

                // Count files before deletion
                $files = File::allFiles($fullPath);
                $fileCount = count($files);

                if ($fileCount > 0) {
                    if (!$this->option('force')) {
                        if (!$this->confirm("Found {$fileCount} signature files in {$directory}. Delete them?")) {
                            $this->info("⏭️  Skipped: {$directory}");
                            continue;
                        }
                    }

                    // Delete the entire directory
                    File::deleteDirectory($fullPath);
                    $deletedFiles += $fileCount;
                    $deletedDirs++;

                    $this->info("✅ Deleted {$fileCount} signature files from {$directory}");
                } else {
                    $this->info("📭 Directory {$directory} is empty, removing directory...");
                    File::deleteDirectory($fullPath);
                    $deletedDirs++;
                }
            } else {
                $this->info("ℹ️  Directory not found: {$directory}");
            }
        }

        // Clean up using Storage facade as well
        if (Storage::disk('public')->exists('signatures')) {
            $storageFiles = Storage::disk('public')->allFiles('signatures');
            $storageFileCount = count($storageFiles);

            if ($storageFileCount > 0) {
                if (!$this->option('force')) {
                    if (!$this->confirm("Found {$storageFileCount} signature files in storage. Delete them?")) {
                        $this->info("⏭️  Skipped storage cleanup");
                    } else {
                        Storage::disk('public')->deleteDirectory('signatures');
                        $deletedFiles += $storageFileCount;
                        $this->info("✅ Deleted {$storageFileCount} signature files from storage");
                    }
                } else {
                    Storage::disk('public')->deleteDirectory('signatures');
                    $deletedFiles += $storageFileCount;
                    $this->info("✅ Deleted {$storageFileCount} signature files from storage");
                }
            }
        }

        // Summary
        $this->newLine();
        $this->info("🎉 Cleanup completed!");
        $this->info("📊 Summary:");
        $this->info("   • Files deleted: {$deletedFiles}");
        $this->info("   • Directories removed: {$deletedDirs}");

        if ($deletedFiles > 0) {
            $this->warn("💡 Remember to also remove any signature-related backup files manually if they exist outside the Laravel storage.");
        }

        return Command::SUCCESS;
    }
}
