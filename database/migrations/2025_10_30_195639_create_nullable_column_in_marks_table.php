<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            $table->decimal('mark', 5, 1)->nullable()->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            $table->decimal('mark', 5, 1)->nullable(false)->default(0)->change();
        });
    }
};
