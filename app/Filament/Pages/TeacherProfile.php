<?php

namespace App\Filament\Pages;

use App\Jobs\FetchCertificateExpiry;
use App\Models\Teacher;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;


class TeacherProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Mening profilim';
    protected static string $view = 'filament.pages.teacher-profile';

    public ?array $data = [];
    public Teacher $teacher;

    public static function canAccess(): bool
    {
        return auth()->user()->role_id == 1;
    }


    public function mount(): void
    {
        $user = auth()->user();
        $this->teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        $this->form->fill([
            'passport_serial_number' => $this->teacher->passport_serial_number,
            'passport_jshshir' => $this->teacher->passport_jshshir,
            'passport_photo_path' => $this->teacher->passport_photo_path,
            'diplom_path' => $this->teacher->diplom_path,
            'malaka_toifa_daraja' => $this->teacher->malaka_toifa_daraja,
            'malaka_toifa_path' => $this->teacher->malaka_toifa_path,
            'milliy_sertifikat1_path' => $this->teacher->milliy_sertifikat1_path,
            'milliy_sertifikat2_path' => $this->teacher->milliy_sertifikat2_path,
            'xalqaro_sertifikat_path' => $this->teacher->xalqaro_sertifikat_path,
            'ustama_sertifikat_path' => $this->teacher->ustama_sertifikat_path,
            'vazir_buyruq_path' => $this->teacher->vazir_buyruq_path,
            'qoshimcha_ustama_path' => $this->teacher->qoshimcha_ustama_path,
            'malumotnoma_path' => $this->teacher->malumotnoma_path,
            'telegram_id' => $this->teacher->telegram_id,
            'profile_photo_path' => $this->teacher->profile_photo_path,
        ]);

        $this->checkExistingDocuments();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('👤 Pasport maʼlumotlari')
                    ->description('Iltimos, pasport maʼlumotlaringizni to\'liq va to\'g\'ri kiriting')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextInput::make('passport_serial_number')
                            ->label('Pasport seriya raqami')
                            ->placeholder('Masalan: AB1234567')
                            ->prefixIcon('heroicon-o-document-text')
                            ->maxLength(50)
                            ->rules(['regex:/^[A-Z]{2}\d{7}$/'])
                            ->validationMessages([
                                'regex' => 'Pasport seriya raqami noto\'g\'ri formatda (Masalan: AB1234567)'
                            ]),

                        TextInput::make('passport_jshshir')
                            ->label('JSHSHIR')
                            ->placeholder('14 xonali JSHSHIR raqamini kiriting')
                            ->prefixIcon('heroicon-o-finger-print')
                            ->mask('99999999999999')
                            ->length(14)
                            ->numeric()
                            ->rules(['digits:14'])
                            ->validationMessages([
                                'digits' => 'JSHSHIR 14 ta raqamdan iborat bo\'lishi kerak'
                            ]),

                        FileUpload::make('profile_photo_path')
                            ->label('📸 Shaxsiy rasm (3x4)')
                            ->disk('public')
                            ->directory('teacher-documents/profile-photos')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['3:4'])
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->maxFiles(1)
                            ->helperText('Rasmingiz aniq va sifatli bo\'lishi kerak (maksimal 5MB)')
                            ->imagePreviewHeight('150')
                            ->uploadingMessage('Rasm yuklanmoqda...'),


                        FileUpload::make('passport_photo_path')
                            ->label('🗂️ Pasport nusxasi')
                            ->disk('public')
                            ->directory('teacher-documents/passport-photos')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['3:4', '4:3', '1:1'])
                            ->maxSize(5120)
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxFiles(1)
                            ->helperText('Pasportning barcha ma\'lumotlari aniq ko\'rinishi kerak')
                            ->uploadingMessage('Pasport yuklanmoqda...'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),

                Section::make('🎓 Ta\'lim hujjatlari')
                    ->description('Ta\'lim va malaka oshirish hujjatlaringizni yuklang')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        FileUpload::make('malumotnoma_path')
                            ->label('📋 Ma\'lumotnoma (obyektivka)')
                            ->disk('public')
                            ->directory('teacher-documents/malumotnoma')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull()
                            ->maxFiles(1)
                            ->helperText('Ishlab chiqarish va pedagogik faoliyat haqidagi ma\'lumotnoma')
                            ->uploadingMessage('Ma\'lumotnoma yuklanmoqda...')
                            ->downloadable(),

                        FileUpload::make('diplom_path')
                            ->label('🏆 Diplom')
                            ->disk('public')
                            ->directory('teacher-documents/diplomas')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull()
                            ->maxFiles(1)
                            ->helperText('Oliy ta\'lim diplomining nusxasi')
                            ->uploadingMessage('Diplom yuklanmoqda...')
                            ->downloadable(),

                        Select::make('malaka_toifa_daraja')
                            ->label('🏅 Malaka toifa daraja')
                            ->options([
                                'mutaxasis' => '👨‍🎓 Mutaxasis',
                                '2-toifa' => '🥉 Ikkinchi toifa',
                                '1-toifa' => '🥈 Birinchi toifa',
                                'oliy-toifa' => '🥇 Oliy toifa',
                            ])
                            ->columnSpanFull()
                            ->reactive()
                            ->placeholder('Malaka toifa darajasini tanlang')
                            ->helperText('Joriy malaka toifa darajangizni tanlang'),

                        FileUpload::make('malaka_toifa_path')
                            ->label('📜 Malaka toifa hujjati')
                            ->disk('public')
                            ->directory('teacher-documents/malaka-toifa')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull()
                            ->maxFiles(1)
                            ->visible(fn (Get $get) => $get('malaka_toifa_daraja') && $get('malaka_toifa_daraja') !== 'mutaxasis')
                            ->required(fn (Get $get) => $get('malaka_toifa_daraja') && $get('malaka_toifa_daraja') !== 'mutaxasis')
                            ->uploadingMessage('Hujjat yuklanmoqda...')
                            ->downloadable(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(true),

                Section::make('🏅 Sertifikatlar')
                    ->description('Milliy va xalqaro sertifikatlaringizni yuklang')
                    ->icon('heroicon-o-trophy')
                    ->schema([
                        FileUpload::make('milliy_sertifikat1_path')
                            ->label('🇺🇿 Birinchi milliy sertifikat')
                            ->disk('public')
                            ->directory('teacher-documents/milliy-sertifikat1')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull()
                            ->maxFiles(1)
                            ->helperText('Birinchi milliy sertifikat nusxasi')
                            ->uploadingMessage('Sertifikat yuklanmoqda...')
                            ->downloadable(),

                        FileUpload::make('milliy_sertifikat2_path')
                            ->label('🇺🇿 Ikkinchi milliy sertifikat')
                            ->disk('public')
                            ->directory('teacher-documents/milliy-sertifikat2')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull()
                            ->maxFiles(1)
                            ->helperText('Ikkinchi milliy sertifikat nusxasi')
                            ->uploadingMessage('Sertifikat yuklanmoqda...')
                            ->downloadable(),

                        FileUpload::make('xalqaro_sertifikat_path')
                            ->label('🌍 Xalqaro sertifikat')
                            ->disk('public')
                            ->directory('teacher-documents/xalqaro-sertifikat')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull()
                            ->maxFiles(1)
                            ->helperText('Xalqaro sertifikat nusxasi (CEFR, IELTS, TOEFL va boshqalar)')
                            ->uploadingMessage('Sertifikat yuklanmoqda...')
                            ->downloadable(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(true),

                Section::make('💰 Ustama hujjatlari')
                    ->description('Ustama to\'lov buyruqlari va sertifikatlari')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        FileUpload::make('vazir_buyruq_path')
                            ->label('📋 Vazir jamg\'armasi buyrug\'i')
                            ->disk('public')
                            ->directory('teacher-documents/vazir-buyruq')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull()
                            ->maxFiles(1)
                            ->helperText('Vazir jamg\'armasi to\'lovi buyicha buyruq nusxasi')
                            ->uploadingMessage('Buyruq yuklanmoqda...')
                            ->downloadable(),

                        FileUpload::make('ustama_sertifikat_path')
                            ->label('📊 70% ustama sertifikati')
                            ->disk('public')
                            ->directory('teacher-documents/ustama-sertifikat')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull()
                            ->maxFiles(1)
                            ->helperText('70% ustama to\'lov sertifikati')
                            ->uploadingMessage('Sertifikat yuklanmoqda...')
                            ->downloadable(),

                        FileUpload::make('qoshimcha_ustama_path')
                            ->label('📄 Qo\'shimcha ustama hujjati')
                            ->disk('public')
                            ->directory('teacher-documents/qoshimcha-ustama')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull()
                            ->maxFiles(1)
                            ->helperText('Qo\'shimcha ustama to\'lovga oid boshqa hujjatlar')
                            ->uploadingMessage('Hujjat yuklanmoqda...')
                            ->downloadable(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(true),

                Section::make('📱 Aloqa ma\'lumotlari')
                    ->description('Qo\'shimcha aloqa ma\'lumotlaringizni kiriting')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        TextInput::make('telegram_id')
                            ->label('Telegram')
                            ->placeholder('@username yoki raqamli ID')
                            ->prefixIcon('heroicon-o-chat-bubble-left-right')
                            ->maxLength(100)
                            ->columnSpanFull()
                            ->helperText('Telegram username (@bilan) yoki raqamli ID ni kiriting')
                            ->rules(['regex:/^@?[a-zA-Z0-9_]+$/'])
                            ->validationMessages([
                                'regex' => 'Telegram ID formati noto\'g\'ri (@username yoki raqam)'
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(true),
            ])
            ->statePath('data')
            ->columns(1);
    }

    private function checkExistingDocuments()
    {
        $certificateFields = [
            'malaka_toifa_path',
            'milliy_sertifikat1_path',
            'milliy_sertifikat2_path',
            'xalqaro_sertifikat_path',
            'ustama_sertifikat_path'
        ];

        foreach ($certificateFields as $field) {
            if ($this->teacher->{$field}) {
                $cacheKey = "teacher:{$this->teacher->id}:cert:{$field}:expires_at";
                $cachedValue = Cache::get($cacheKey);

                // Only dispatch job if NO cache exists at all (first time processing)
                // Skip if any cache exists: 'no_expiry', 'no_document', 'error', or actual date
                // This prevents re-dispatching jobs on every page refresh for failed certificates
                if (is_null($cachedValue)) {
                    FetchCertificateExpiry::dispatch($this->teacher->id, $field, $cacheKey);
                }
            }
        }
    }

    public function save()
    {
        $data = $this->form->getState();
        $this->teacher->update($data);

        $certificateFields = [
            'malaka_toifa_path',
            'milliy_sertifikat1_path',
            'milliy_sertifikat2_path',
            'xalqaro_sertifikat_path',
            'ustama_sertifikat_path'
        ];

        foreach ($certificateFields as $field) {
            if ($this->teacher->{$field}) {
                $cacheKey = "teacher:{$this->teacher->id}:cert:{$field}:expires_at";
                $expire_date = Cache::get($cacheKey);

                // Only dispatch job if no cache exists OR if document was just updated
                // Skip if cache exists with 'no_expiry' value
                if (is_null($expire_date) || (isset($data[$field]) && $expire_date !== 'no_expiry')) {
                    Cache::forget($cacheKey);
                    FetchCertificateExpiry::dispatch($this->teacher->id, $field, $cacheKey);
                }
            }
        }

        Notification::make()
            ->title('Profil muvaffaqiyatli yangilandi')
            ->success()
            ->send();
    }



    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Profilni yangilash')
                ->submit('save')
                ->color('primary'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Oʻqituvchi profili - ' . $this->teacher->full_name;
    }
}
