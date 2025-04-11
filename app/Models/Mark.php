<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mark extends Model
{
    /** @use HasFactory<\Database\Factories\MarkFactory> */
    use HasFactory;

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
}
