<?php

namespace App\Console\Commands;

use Filament\Actions\Imports\Models\FailedImportRow;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupOldFailedImportRows extends Command
{
    protected $signature = 'imports:cleanup-failed-rows';

    protected $description = 'Delete failed import rows older than 30 days';

    public function handle(): int
    {
        $deleted = FailedImportRow::where('created_at', '<', now()->subDays(30))
            ->delete();

        $this->info("Deleted {$deleted} failed import rows older than 30 days.");

        return Command::SUCCESS;
    }
}
