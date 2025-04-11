<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Problem extends Model
{
    /** @use HasFactory<\Database\Factories\ProblemFactory> */
    use HasFactory;

    public function exam():BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
    public function marks():hasMany
    {
        return $this->hasMany(Mark::class);
    }
}
