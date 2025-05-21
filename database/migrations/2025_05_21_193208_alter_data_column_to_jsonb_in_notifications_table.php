<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Convert `data` column from text to jsonb
        DB::statement(/** @lang text */ 'ALTER TABLE notifications ALTER COLUMN data TYPE jsonb USING data::jsonb');
    }

    public function down(): void
    {
        // Revert back to text if needed
        DB::statement(/** @lang text */ 'ALTER TABLE notifications ALTER COLUMN data TYPE text USING data::text');
    }
};

