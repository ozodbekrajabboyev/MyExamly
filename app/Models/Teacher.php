<?php

namespace App\Models;

use App\Traits\ScopesSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms;
use Illuminate\Support\Facades\Storage;

class Teacher extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory, ScopesSchool;

    public function exams():HasMany
    {
        return $this->hasMany(Exam::class);
    }
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject', 'teacher_id', 'subject_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function maktab(): BelongsTo
    {
        return $this->belongsTo(Maktab::class);
    }
    public function getPassportPhotoUrlAttribute(): ?string
    {
        return $this->passport_photo_path ? Storage::disk('public')->url($this->passport_photo_path) : null;
    }

    public function getDiplomUrlAttribute(): ?string
    {
        return $this->diplom_path ? Storage::disk('public')->url($this->diplom_path) : null;
    }

    public function getMalakaToifaUrlAttribute(): ?string
    {
        return $this->malaka_toifa_path ? Storage::disk('public')->url($this->malaka_toifa_path) : null;
    }

    public function getMilliySertifikatUrlAttribute(): ?string
    {
        return $this->milliy_sertifikat_path ? Storage::disk('public')->url($this->milliy_sertifikat_path) : null;
    }

    public function getXalqaroSertifikatUrlAttribute(): ?string
    {
        return $this->xalqaro_sertifikat_path ? Storage::disk('public')->url($this->xalqaro_sertifikat_path) : null;
    }

    public function getMalumotnomaUrlAttribute(): ?string
    {
        if (!$this->malumotnoma_path) {
            return null;
        }
        return Storage::disk('public')->url($this->malumotnoma_path);
    }

    public function getSignatureUrlAttribute(): ?string
    {
        if (!$this->signature_path) {
            return null;
        }
        return Storage::disk('public')->url($this->signature_path);
    }

    public function hasSignature(): bool
    {
        return !empty($this->signature_path);
    }
}
