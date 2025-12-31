<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing FB marks from 4 to 0 (since 4 was the old default)
        DB::table('fb_marks')
            ->where('fb', 4)
            ->update(['fb' => 0]);

        // Since PostgreSQL doesn't support direct constraint modification,
        // we need to drop and recreate the column with new constraints
        Schema::table('fb_marks', function (Blueprint $table) {
            // Remove the old constraints by dropping and recreating the column
            $table->dropColumn('fb');
        });

        Schema::table('fb_marks', function (Blueprint $table) {
            // Add the column back with new constraints: min(0), max(10), default(0)
            $table->integer('fb')->min(0)->max(10)->default(0)->after('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to old constraints: min(4), max(10), default(4)
        Schema::table('fb_marks', function (Blueprint $table) {
            $table->dropColumn('fb');
        });

        Schema::table('fb_marks', function (Blueprint $table) {
            $table->integer('fb')->min(4)->max(10)->default(4)->after('student_id');
        });
    }
};

