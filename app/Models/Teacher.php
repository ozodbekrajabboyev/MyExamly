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

    protected $fillable = [
        'maktab_id',
        'full_name',
        'user_id',
        'phone',
        'passport_serial_number',
        'passport_jshshir',
        'passport_photo_path',
        'diplom_path',
        'malaka_toifa_daraja',
        'malaka_toifa_path',
        'malaka_toifa_expdate',
        'milliy_sertifikat1_path',
        'milliy_sertifikat1_expdate',
        'milliy_sertifikat2_path',
        'milliy_sertifikat2_expdate',
        'xalqaro_sertifikat_path',
        'xalqaro_sertifikat_expdate',
        'malumotnoma_path',
        'ustama_sertifikat_path',
        'ustama_sertifikat_expdate',
        'vazir_buyruq_path',
        'qoshimcha_ustama_path',
        'telegram_id',
        'profile_photo_path',
    ];

    protected $casts = [
        'malaka_toifa_expdate' => 'date',
        'milliy_sertifikat1_expdate' => 'date',
        'milliy_sertifikat2_expdate' => 'date',
        'xalqaro_sertifikat_expdate' => 'date',
        'ustama_sertifikat_expdate' => 'date',
    ];


    // Certificate field configuration methods

    public static function getCertificateFields(): array
    {
        return [
            'malaka_toifa_path' => [
                'label' => 'Malaka toifasi',
                'expiry_field' => 'malaka_toifa_expdate'
            ],
            'milliy_sertifikat1_path' => [
                'label' => '1-milliy sertifikat',
                'expiry_field' => 'milliy_sertifikat1_expdate'
            ],
            'milliy_sertifikat2_path' => [
                'label' => '2-milliy sertifikat',
                'expiry_field' => 'milliy_sertifikat2_expdate'
            ],
            'xalqaro_sertifikat_path' => [
                'label' => 'Xalqaro sertifikat',
                'expiry_field' => 'xalqaro_sertifikat_expdate'
            ],
            'ustama_sertifikat_path' => [
                'label' => 'Ustama sertifikat',
                'expiry_field' => 'ustama_sertifikat_expdate'
            ],
        ];
    }


    public function getCertificateExpiryStatus(string $field): ?array
    {
        $certificateFields = self::getCertificateFields();

        if (!isset($certificateFields[$field])) {
            return null;
        }

        $expiryField = $certificateFields[$field]['expiry_field'];
        $expiryDate = $this->{$expiryField};

        if (!$expiryDate) {
            return null;
        }

        try {
            $tz = 'Asia/Tashkent';
            $expiry = Carbon::parse($expiryDate)->startOfDay();
            $today = Carbon::now($tz)->startOfDay();
            $daysLeft = $today->diffInDays($expiry, false);

            return [
                'expires_at' => $expiry,
                'days_left' => $daysLeft,
                'is_expired' => $daysLeft < 0,
                'is_expiring_soon' => $daysLeft >= 0 && $daysLeft <= 3,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    public function getAllCertificatesStatus(): array
    {
        $out = [];
        foreach (self::getCertificateFields() as $field => $config) {
            $out[$field] = [
                'label' => $config['label'],
                'status' => $this->getCertificateExpiryStatus($field),
            ];
        }
        return $out;
    }


    // Relationships

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
