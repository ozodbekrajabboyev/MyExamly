<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check for existing duplicates before adding constraint
        $duplicates = DB::table('exams')
            ->select('sinf_id', 'subject_id', 'serial_number', DB::raw('count(*) as duplicate_count'))
            ->groupBy('sinf_id', 'subject_id', 'serial_number')
            ->havingRaw('count(*) > 1')
            ->get();

        if ($duplicates->count() > 0) {
            echo "Warning: Found duplicate serial numbers that need to be resolved:\n";
            foreach ($duplicates as $duplicate) {
                echo "Sinf ID: {$duplicate->sinf_id}, Subject ID: {$duplicate->subject_id}, Serial: {$duplicate->serial_number} (Count: {$duplicate->duplicate_count})\n";
            }
            throw new \Exception('Please resolve duplicate serial numbers before running this migration.');
        }
    }

    public function down(): void
    {
        // Nothing to do here
    }
};
