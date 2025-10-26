<?php

namespace App\Models;

use App\Traits\ScopesSchool;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Teacher extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory, ScopesSchool, Notifiable;


    // Add these methods to your existing Teacher model

    /**
     * Set certificate expiry for testing
     */
    public function setCertificateExpiryForTesting(string $field, string $date): bool
    {
        $fields = self::getCertificateFields();
        if (!array_key_exists($field, $fields)) {
            throw new \InvalidArgumentException("Invalid field: {$field}");
        }

        $cacheKey = "teacher:{$this->id}:cert:{$field}:expires_at";
        $result = Cache::put($cacheKey, $date, now()->addDays(30));

        Log::info("Certificate expiry set for testing", [
            'teacher_id' => $this->id,
            'field' => $field,
            'expiry_date' => $date,
            'cache_result' => $result
        ]);

        return $result;
    }

    /**
     * Get all cache keys for debugging
     */
    public function getCertificateCacheInfo(): array
    {
        $info = [];
        foreach (self::getCertificateFields() as $field => $label) {
            $cacheKey = "teacher:{$this->id}:cert:{$field}:expires_at";
            $info[$field] = [
                'label' => $label,
                'cache_key' => $cacheKey,
                'cached_value' => Cache::get($cacheKey),
                'has_file' => !empty($this->{$field}),
                'file_path' => $this->{$field}
            ];
        }
        return $info;
    }

    public static function getCertificateFields(): array
    {
        return [
            'malaka_toifa_path' => 'Malaka toifasi',
            'milliy_sertifikat1_path' => '1-milliy sertifikat',
            'milliy_sertifikat2_path' => '2-milliy sertifikat',
            'xalqaro_sertifikat_path' => 'Xalqaro sertifikat',
            'ustama_sertifikat_path' => 'Ustama sertifikat',
        ];
    }


    public function getCertificateExpiryStatus(string $field): ?array
    {
        $cacheKey = "teacher:{$this->id}:cert:{$field}:expires_at";
        $value = Cache::get($cacheKey);
        if (! $value) return null;

        try {
            $tz = 'Asia/Tashkent';
            $expiry   = Carbon::parse($value, $tz)->startOfDay();
            $today    = Carbon::now($tz)->startOfDay();
            $daysLeft = $today->diffInDays($expiry, false);

            return [
                'expires_at'       => $expiry,
                'days_left'        => $daysLeft,
                'is_expired'       => $daysLeft < 0,
                'is_expiring_soon' => $daysLeft >= 0 && $daysLeft <= 3,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    public function getAllCertificatesStatus(): array
    {
        $out = [];
        foreach (self::getCertificateFields() as $field => $label) {
            $out[$field] = [
                'label'      => $label,
                'status'     => $this->getCertificateExpiryStatus($field),
            ];
        }
        return $out;
    }


    protected static function booted()
    {
        static::updated(function (self $teacher) {
            $certFields = [
                'malaka_toifa_path',
                'milliy_sertifikat1_path',
                'milliy_sertifikat2_path',
                'xalqaro_sertifikat_path',
                'ustama_sertifikat_path',
            ];

            foreach ($certFields as $field) {
                if ($teacher->wasChanged($field)) {
                    $cacheKey = "teacher:{$teacher->id}:cert:{$field}:expires_at";
                    Cache::forget($cacheKey);
                }
            }
        });
    }

    public function exams():BelongsToMany
    {
        return $this->belongsToMany(Exam::class);
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
    public function getMilliySertifikat1UrlAttribute(): ?string
    {
        return $this->milliy_sertifikat1_path ? Storage::disk('public')->url($this->milliy_sertifikat1_path) : null;
    }

    public function getMilliySertifikat2UrlAttribute(): ?string
    {
        return $this->milliy_sertifikat2_path ? Storage::disk('public')->url($this->milliy_sertifikat2_path) : null;
    }

    public function getUstamaSertifikatUrlAttribute(): ?string
    {
        return $this->ustama_sertifikat_path ? Storage::disk('public')->url($this->ustama_sertifikat_path) : null;
    }

    public function getVazirBuyruqUrlAttribute(): ?string
    {
        return $this->vazir_buyruq_path ? Storage::disk('public')->url($this->vazir_buyruq_path) : null;
    }

    public function getQoshimchaUstamaUrlAttribute(): ?string
    {
        return $this->qoshimcha_ustama_path ? Storage::disk('public')->url($this->qoshimcha_ustama_path) : null;
    }


    // In App/Models/Teacher.php
    public function getLavozimAttribute(): string
    {
        $this->loadMissing(['subjects', 'maktab']);

        $maktabName = $this->maktab?->name ?? 'Maktab';

        if ($this->subjects && $this->subjects->count() > 0) {
            $subjectNames = $this->subjects->pluck('name')->toArray();

            if (count($subjectNames) === 1) {
                return "{$maktabName}da {$subjectNames[0]} fani o'qituvchisi";
            } else {
                $lastSubject = array_pop($subjectNames);
                $subjectsText = implode(', ', $subjectNames) . ' va ' . $lastSubject;
                return "{$maktabName}da {$subjectsText} fanlari o'qituvchisi";
            }
        }

        return "{$maktabName}da o'qituvchi";
    }


    public function isDocumentRequired(string $documentField): bool
    {
        $documentsNotRequiredForMutaxasis = [
            'malaka_toifa_path'
        ];

        return !($this->malaka_toifa_daraja === 'mutaxasis' &&
            in_array($documentField, $documentsNotRequiredForMutaxasis));
    }

    public function getDocumentStatusMessage(string $documentField): string
    {
        if (!$this->isDocumentRequired($documentField)) {
            return match($documentField) {
                'malaka_toifa_path' => 'Mutaxasis uchun malaka toifa hujjati talab qilinmaydi',
                default => 'Bu hujjat talab qilinmaydi'
            };
        }

        return $this->$documentField ? 'Hujjatni ko\'rish' : 'Hujjat yuklanmagan';
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

    public static function getForm(): array
    {
        return [
            Section::make("Yangi o'qituvchi qo'shish")
                ->collapsible()
                ->columns(1)
                ->description("Yangi o'qituvchi qo'shish uchun quyidagilarni to'ldiring")
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Hidden::make('maktab_id')
                        ->default(fn () => auth()->user()->maktab_id)
                        ->required(),
                    TextInput::make('full_name')
                        ->label("O'qituvchining to'liq IFSH")
                        ->helperText("O'qituvchining to'liq Ism, Familiya va Sharifini kiriting.")
                        ->columnSpanFull()
                        ->required(),
                    TextInput::make('email')
                        ->label("O'qituvchining emailini kiriting")
                        ->required()
                        ->placeholder("example@example.com")
                        ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                        ->columnSpanFull(),
                    Select::make('subjects')
                        ->label("Fanni tanlang")
                        ->helperText("O'qituvchining fan(lar)ini tanlang.")
                        ->hint("Bir nechta fanni tanlashingiz mumkin")
                        ->columnSpanFull()
                        ->required()
                        ->relationship('subjects', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    TextInput::make('phone')
                        ->label("Telefon raqam")
                        ->helperText("O'qituvchining telefon raqamini kiriting.")
                        ->columnSpanFull()
                        ->prefix('+998')
                        ->tel(),
                ]),
        ];
    }
}
