<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maktab extends Model
{
    /** @use HasFactory<\Database\Factories\MaktabFactory> */
    use HasFactory;


    public function users(): HasMany
    {
        return $this->HasMany(User::class);
    }

    public function teachers(): HasMany
    {
        return $this->HasMany(Teacher::class);
    }

    public function sinfs(): HasMany
    {
        return $this->HasMany(Sinf::class);
    }

    public function subjects(): HasMany
    {
        return $this->HasMany(Subject::class);
    }

    public function students(): HasMany
    {
        return $this->HasMany(Student::class);
    }

    public function exams(): HasMany
    {
        return $this->HasMany(Exam::class);
    }

    public function problems(): HasMany
    {
        return $this->HasMany(Problem::class);
    }

    public function marks(): HasMany
    {
        return $this->HasMany(Mark::class);
    }


}
