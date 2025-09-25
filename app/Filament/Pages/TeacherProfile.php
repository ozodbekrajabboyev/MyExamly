<?php

namespace App\Filament\Pages;

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
            'signature_path' => $this->teacher->signature_path,
            'telegram_id' => $this->teacher->telegram_id,
            'profile_photo_path' => $this->teacher->profile_photo_path,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pasport maʼlumotlari')
                    ->description('Iltimos, pasport maʼlumotlaringizni kiriting')
                    ->schema([
                        TextInput::make('passport_serial_number')
                            ->label('Pasport seriya raqami')
                            ->placeholder('Masalan: AB1234567')
                            ->maxLength(50),

                        TextInput::make('passport_jshshir')
                            ->label('Pasport JSHSHIR')
                            ->placeholder('JSHSHIR raqamini kiriting')
                            ->maxLength(50),


                        FileUpload::make('profile_photo_path')
                            ->label('Rasmingizni yuklang (3x4)')
                            ->disk('public')
                            ->directory('teacher-documents/profile-photos')
                            ->image()
//                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '3:4',
                            ])
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'])
                            ->maxFiles(1),

                        FileUpload::make('signature_path')
                            ->label("Shaxsiy imzoni yuklang")
                            ->image()
                            ->acceptedFileTypes(['image/png', 'image/svg+xml'])
                            ->maxSize(5096)
                            ->directory('signatures')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText("PNG yoki SVG formatda imzo rasmini yuklang"),

                        FileUpload::make('passport_photo_path')
                            ->label('Pasportingizni yuklang')
                            ->disk('public')
                            ->directory('teacher-documents/passport-photos')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '3:4',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'])
                            ->maxFiles(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Taʼlimga oid hujjatlar')
                    ->description('Xodimning shaxsiy hujjatlari')
                    ->schema([
                        FileUpload::make('malumotnoma_path')
                            ->label("Maʼlumotnoma (obyektivka)")
                            ->disk('public')
                            ->directory('teacher-documents/malumotnoma')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull()
                            ->maxFiles(1),

                        FileUpload::make('diplom_path')
                            ->label('Diplom')
                            ->disk('public')
                            ->directory('teacher-documents/diplomas')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull()
                            ->maxFiles(1),

                        Select::make('malaka_toifa_daraja')
                            ->label('Malaka toifa daraja')
                            ->options([
                                'mutaxasis' => 'Mutaxasis',
                                '2-toifa' => 'Ikkinchi toifa',
                                '1-toifa' => 'Birinchi toifa',
                                'oliy-toifa' => 'Oliy toifa',
                            ])
                            ->columnSpanFull()
                            ->reactive(),

                        FileUpload::make('malaka_toifa_path')
                            ->label('Malaka toifa hujjati')
                            ->disk('public')
                            ->directory('teacher-documents/malaka-toifa')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull()
                            ->maxFiles(1)
                            ->visible(fn (Get $get) => $get('malaka_toifa_daraja') && $get('malaka_toifa_daraja') !== 'mutaxasis')
                            ->required(fn (Get $get) => $get('malaka_toifa_daraja') && $get('malaka_toifa_daraja') !== 'mutaxasis'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Sertifikatlar')
                    ->description('Milliy va xalqaro sertifikatlaringizni yuklang')
                    ->schema([
                        FileUpload::make('milliy_sertifikat1_path')
                            ->label('Milliy sertifikat #1')
                            ->disk('public')
                            ->directory('teacher-documents/milliy-sertifikat1')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull()
                            ->maxFiles(1),

                        FileUpload::make('milliy_sertifikat2_path')
                            ->label('Milliy sertifikat #2')
                            ->disk('public')
                            ->directory('teacher-documents/milliy-sertifikat2')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull()
                            ->maxFiles(1),

                        FileUpload::make('xalqaro_sertifikat_path')
                            ->label('Xalqaro sertifikat')
                            ->disk('public')
                            ->directory('teacher-documents/xalqaro-sertifikat')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull()
                            ->maxFiles(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Ustama hujjatlar')
                    ->description('Ustama buyruq va sertifikatlar')
                    ->schema([
                        FileUpload::make('vazir_buyruq_path')
                            ->label('Vazir jamg\'armasi to\'lovi buyicha buyruq')
                            ->disk('public')
                            ->directory('teacher-documents/vazir-buyruq')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull()
                            ->maxFiles(1),

                        FileUpload::make('ustama_sertifikat_path')
                            ->label('70% ustama sertifikati')
                            ->disk('public')
                            ->directory('teacher-documents/ustama-sertifikat')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull()
                            ->maxFiles(1),

                        FileUpload::make('qoshimcha_ustama_path')
                            ->label("Qo'shimcha ustama hujjati")
                            ->disk('public')
                            ->directory('teacher-documents/qoshimcha-ustama')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull()
                            ->maxFiles(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Aloqa maʼlumotlari')
                    ->description('Qoʻshimcha aloqaga oid maʼlumotlar')
                    ->schema([
                        TextInput::make('telegram_id')
                            ->label('Telegram ID')
                            ->placeholder('@foydalanuvchi yoki raqamli ID')
                            ->maxLength(100)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->teacher->update($data);

        Notification::make()
            ->title('Profil yangilandi')
            ->body('Profil maʼlumotlaringiz muvaffaqiyatli yangilandi.')
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
