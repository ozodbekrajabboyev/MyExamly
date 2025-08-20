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
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class TeacherProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Mening Profilim';
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
            'malaka_toifa_path' => $this->teacher->malaka_toifa_path,
            'milliy_sertifikat_path' => $this->teacher->milliy_sertifikat_path,
            'xalqaro_sertifikat_path' => $this->teacher->xalqaro_sertifikat_path,
            'malumotnoma_path' => $this->teacher->malumotnoma_path,
            'telegram_id' => $this->teacher->telegram_id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pasport Maʼlumotlari')
                    ->description('Iltimos, pasport maʼlumotlaringizni kiriting')
                    ->schema([
                        TextInput::make('passport_serial_number')
                            ->label('Pasport Seriya Raqami')
                            ->placeholder('Masalan: AB1234567')
                            ->maxLength(50),

                        TextInput::make('passport_jshshir')
                            ->label('Pasport JSHSHIR')
                            ->placeholder('JSHSHIR raqamini kiriting')
                            ->maxLength(50),

                        FileUpload::make('passport_photo_path')
                            ->label('Pasport Fotosurati')
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
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->maxFiles(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Taʼlimga Oid Hujjatlar')
                    ->description('Diplom va boshqa malaka hujjatlaringizni yuklang')
                    ->schema([
                        FileUpload::make('diplom_path')
                            ->label('Diplom')
                            ->disk('public')
                            ->directory('teacher-documents/diplomas')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxFiles(1),

                        FileUpload::make('malaka_toifa_path')
                            ->label('Malaka Toifa')
                            ->disk('public')
                            ->directory('teacher-documents/malaka-toifa')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxFiles(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Sertifikatlar')
                    ->description('Milliy va xalqaro sertifikatlaringizni yuklang')
                    ->schema([
                        FileUpload::make('milliy_sertifikat_path')
                            ->label('Milliy Sertifikat')
                            ->disk('public')
                            ->directory('teacher-documents/milliy-sertifikat')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxFiles(1),

                        FileUpload::make('xalqaro_sertifikat_path')
                            ->label('Xalqaro Sertifikat')
                            ->disk('public')
                            ->directory('teacher-documents/xalqaro-sertifikat')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxFiles(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Qoʻshimcha Hujjatlar va Aloqa')
                    ->description('Qoʻshimcha hujjatlar va aloqaga oid maʼlumotlar')
                    ->schema([
                        FileUpload::make('malumotnoma_path')
                            ->label("Maʼlumotnoma")
                            ->disk('public')
                            ->directory('teacher-documents/malumotnoma')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxFiles(1),

                        TextInput::make('telegram_id')
                            ->label('Telegram ID')
                            ->placeholder('@foydalanuvchi yoki raqamli ID')
                            ->maxLength(100),
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
            ->title('Profil Yangilandi')
            ->body('Profil maʼlumotlaringiz muvaffaqiyatli yangilandi.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Profilni Yangilash')
                ->submit('save')
                ->color('primary'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Oʻqituvchi Profili - ' . $this->teacher->full_name;
    }
}
