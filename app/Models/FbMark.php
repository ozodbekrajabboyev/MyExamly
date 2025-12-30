<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FbMark extends Model
{
    use HasFactory;

    protected $table = 'fb_marks';

    protected $fillable = [
        'quarter',
        'sinf_id',
        'subject_id',
        'student_id',
        'fb'
    ];

    protected $casts = [
        'fb' => 'integer'
    ];

    /**
     * Get the student that owns the fb mark.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject that owns the fb mark.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the class (sinf) that owns the fb mark.
     */
    public function sinf(): BelongsTo
    {
        return $this->belongsTo(Sinf::class);
    }

    /**
     * Scope to filter by quarter.
     */
    public function scopeQuarter($query, $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    /**
     * Scope to filter by school through relationships.
     * Since FB marks don't directly have maktab_id, we filter through student's sinf
     */
    public function scopeForSchool($query, $maktabId)
    {
        return $query->whereHas('sinf', function ($q) use ($maktabId) {
            $q->where('maktab_id', $maktabId);
        });
    }

    /**
     * Get all available quarters.
     */
    public static function getAvailableQuarters(): array
    {
        return ['I', 'II', 'III', 'IV'];
    }
}
