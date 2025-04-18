<?php

namespace App\Models;

use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mark extends Model
{
    /** @use HasFactory<\Database\Factories\MarkFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'problem_id',
        'exam_id',
        'sinf_id',
        'mark',
    ];

    public function exam():BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function sinf():BelongsTo
    {
        return $this->belongsTo(Sinf::class);
    }

    public function student():BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }

    public static function getForm(){

    }
}

