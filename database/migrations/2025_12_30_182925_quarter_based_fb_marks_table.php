<?php

use App\Models\Student;
use App\Models\Subject;
use App\Models\Sinf;
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
        Schema::create('fb_marks', function (Blueprint $table) {
            $table->id();
            $table->enum('quarter', ['I', 'II', 'III', 'IV'])->nullable();
            $table->foreignIdFor(Sinf::class);
            $table->foreignIdFor(Subject::class);
            $table->foreignIdFor(Student::class);
            $table->integer('fb')->min(4)->max(10)->default(4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
