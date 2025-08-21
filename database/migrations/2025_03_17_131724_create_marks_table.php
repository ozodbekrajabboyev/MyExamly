<?php

use App\Models\Exam;
use App\Models\Maktab;
use App\Models\Problem;
use App\Models\Sinf;
use App\Models\Student;
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
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Maktab::class);
            $table->foreignIdFor(Exam::class);
            $table->foreignIdFor(Sinf::class);
            $table->foreignIdFor(Student::class);
            $table->tinyInteger('problem_id');
            $table->integer('mark');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
