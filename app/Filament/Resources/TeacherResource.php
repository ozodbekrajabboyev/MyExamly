<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers;
use App\Models\District;
use App\Models\Maktab;
use App\Models\Region;
use App\Models\Role;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = "O'qituvchilar";

    protected static ?string $navigationGroup = 'Foydalanuvchilar boshqaruvi';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return "O'qituvchilar soni";
    }

    public static function getModelLabel(): string
    {
        return "O'qituvchi";
    }

    public static function getPluralModelLabel(): string
    {
        return "O'qituvchilar";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Teacher::getForm());
    }

    protected static function afterSave($record, $data)
    {
        $record->subjects()->sync($data['subjects']);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Main Profile Information
                Section::make('Shaxsiy ma\'lumotlar va aloqa')
                    ->icon('heroicon-o-user-circle')
                    ->description('Shaxsiy ma\'lumotlar va aloqa ma\'lumotlari')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // Personal Information Section
                                Section::make('Shaxsiy ma\'lumotlar')
                                    ->icon('heroicon-o-user')
                                    ->description('Xodimning umumiy ma\'lumotlari')
                                    ->schema([
                                        TextEntry::make('full_name')
                                            ->label('To\'liq I.F.SH')
                                            ->size(TextEntry\TextEntrySize::Medium)
                                            ->weight(FontWeight::Bold)
                                            ->color('primary'),
//                                            ->icon('heroicon-o-user-circle'),

                                        ImageEntry::make('profile_photo_path')
                                            ->label('Profil rasmi')
                                            ->size(200)
                                            ->square()
                                            ->visible(fn ($state, $record) =>
                                            $record->profile_photo_path ? true : false),
//                                            ->defaultImageUrl(url('/images/default-avatar.png')),
//                                            ->extraAttributes([
//                                                'class' => 'mx-auto shadow-lg '
//                                            ]),

                                        TextEntry::make('profile_status')
                                            ->default("Profile rasmi mavjud emas")
                                            ->label('Profil rasm holati')
                                            ->formatStateUsing(fn ($state, $record) =>
                                            $record->profile_photo_path ? 'Rasm mavjud' : 'Rasm yuklanmagan')
                                            ->badge()
                                            ->color(fn ($state, $record) => $record->profile_photo_path ? 'success' : 'warning')
                                            ->icon(fn ($state, $record) =>
                                            $record->profile_photo_path ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                                            ->visible(fn ($state, $record) =>
                                            $record->profile_photo_path ? false : true),

                                    ])
                                    ->compact()
                                    ->columnSpan(1),

                                // Professional Information Section
                                Section::make('Kasbiy ma\'lumotlar')
                                    ->icon('heroicon-o-briefcase')
                                    ->description('Ish joyi va kasbiy faoliyat')
                                    ->schema([
                                        TextEntry::make('maktab.name')
                                            ->label('Ish joyi (Maktab)')
                                            ->icon('heroicon-o-building-office-2')
                                            ->color('emerald')
                                            ->weight('medium')
                                            ->placeholder('Maktab kiritilmagan')
                                            ->extraAttributes([
                                                'class' => 'text-wrap break-words whitespace-normal'
                                            ]),

                                        TextEntry::make('malaka_toifa_daraja')
                                            ->label('Malaka toifa daraja')
                                            ->icon('heroicon-o-document-text')
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'purple' : 'gray')
                                            ->placeholder('Malaka darajasi kiritilmagan')
                                            ->formatStateUsing(function ($state) {
                                                if (empty($state)) {
                                                    return 'Daraja belgilanmagan';
                                                }

                                                return match($state) {
                                                    'oliy-toifa' => 'Oliy toifa',
                                                    '1-toifa' => 'Birinchi toifa',
                                                    '2-toifa' => 'Ikkinchi toifa',
                                                    'mutaxasis' => 'Mutaxasis',
                                                    default => $state
                                                };
                                            }),

                                        TextEntry::make('lavozim')
                                            ->label('Lavozim')
                                            ->icon('heroicon-o-identification')
                                            ->color('indigo')
                                            ->weight('medium') // Changed from 'bold' to 'medium'
                                            ->size(TextEntry\TextEntrySize::Small) // Changed from Medium to Small
                                            ->extraAttributes([
                                                'class' => 'text-wrap break-words whitespace-normal text-sm leading-snug'
                                            ]),
                                    ])
                                    ->compact()
                                    ->columnSpan(1),

                                // Contact & Account Information Section
                                Section::make('Aloqa va hisob')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->description('Ijtimoiy tarmoq va hisob ma\'lumotlari')
                                    ->schema([
                                        TextEntry::make('telegram_id')
                                            ->label('Telegram')
                                            ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                            ->copyable()
                                            ->copyMessage('Telegram ID nusxalandi!')
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'cyan' : 'gray')
                                            ->placeholder('Telegram ID kiritilmagan')
                                            ->formatStateUsing(fn ($state) => $state ? "{$state}" : 'ID kiritilmagan'),

                                        TextEntry::make('phone')
                                            ->label('Telefon raqami')
                                            ->icon('heroicon-o-device-phone-mobile')
                                            ->copyable()
                                            ->copyMessage('Telefon nusxalandi!')
                                            ->placeholder('Ma\'lumot kiritilmagan')
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'success' : 'gray')
                                            ->formatStateUsing(fn ($state) => $state ?: 'Telefon kiritilmagan'),

                                        TextEntry::make('user.email')
                                            ->label('Elektron pochta')
                                            ->icon('heroicon-o-envelope')
                                            ->copyable()
                                            ->copyMessage('Email nusxalandi!')
                                            ->badge()
                                            ->color('blue')
                                            ->weight('medium'),

                                        TextEntry::make('created_at')
                                            ->label('Ro\'yxatdan o\'tgan sana')
                                            ->icon('heroicon-o-calendar-days')
                                            ->dateTime('d.m.Y H:i')
                                            ->badge()
                                            ->color('amber')
                                            ->weight('medium'),

                                    ])
                                    ->compact()
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),


                // Identification Section with Passport Photo
                Section::make('Shaxsni tasdiqlovchi hujjatlar')
                    ->icon('heroicon-o-identification')
                    ->description('Rasmiy shaxsni tasdiqlovchi hujjatlar va pasport ma\'lumotlari')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // Passport Documents Section
                                Section::make('Pasport hujjatlari')
                                    ->icon('heroicon-o-document-text')
                                    ->description('Pasport ma\'lumotlari')
                                    ->schema([
                                        TextEntry::make('passport_photo_path')
                                            ->label('Pasport hujjati')
                                            ->formatStateUsing(fn ($state) => $state ? 'Hujjatni ko\'rish' : null)
                                            ->placeholder('Hujjat yuklanmagan')
                                            ->url(fn ($record) => $record->passport_photo_path ?
                                                Storage::disk('public')->url($record->passport_photo_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'success' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-circle'),

                                        TextEntry::make('passport_serial_number')
                                            ->label('Pasport seriyasi')
                                            ->icon('heroicon-o-hashtag')
                                            ->copyable()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->placeholder('Kiritilmagan')
                                            ->formatStateUsing(fn ($state) => $state ?: 'Ma\'lumot kiritilmagan'),
                                    ])
                                    ->compact()
                                    ->columnSpan(1),

                                // Personal Information Section
                                Section::make('Shaxsiy ma\'lumotlar')
                                    ->icon('heroicon-o-user-circle')
                                    ->description('JSHSHIR va shaxsiy hujjatlar')
                                    ->schema([
                                        TextEntry::make('passport_jshshir')
                                            ->label('JSHSHIR')
                                            ->icon('heroicon-o-finger-print')
                                            ->copyable()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'secondary' : 'gray')
                                            ->placeholder('Kiritilmagan')
                                            ->formatStateUsing(fn ($state) => $state ?: 'Ma\'lumot kiritilmagan'),

                                        TextEntry::make('malumotnoma_path')
                                            ->label('Ma\'lumotnoma (obyektivka)')
                                            ->formatStateUsing(fn ($state) => $state ? 'Hujjatni ko\'rish' : null)
                                            ->placeholder('Hujjat yuklanmagan')
                                            ->url(fn ($record) => $record->malumotnoma_path ?
                                                Storage::disk('public')->url($record->malumotnoma_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'success' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-circle'),

                                        // You can add more personal info fields here if needed
                                        TextEntry::make('placeholder_personal')
                                            ->label('')
                                            ->hiddenLabel()
                                            ->default('')
                                            ->extraAttributes(['class' => 'invisible']),
                                    ])
                                    ->compact()
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                // Professional Documents
                Section::make('Kasbiy hujjatlar')
                    ->icon('heroicon-o-academic-cap')
                    ->description('Ta\'lim sertifikatlari va kasbiy hujjatlar')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // Educational Documents Group
                                Section::make('Ta\'lim hujjatlari')
                                    ->icon('heroicon-o-book-open')
                                    ->description('Diplom va malaka toifa hujjatlari')
                                    ->schema([
                                        TextEntry::make('diplom_path')
                                            ->label('Diplom')
                                            ->formatStateUsing(fn ($state) => $state ? 'Hujjatni ko\'rish' : null)
                                            ->placeholder('Hujjat yuklanmagan')
                                            ->url(fn ($record) => $record->diplom_path ?
                                                Storage::disk('public')->url($record->diplom_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'success' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-circle'),

                                        TextEntry::make('malaka_toifa_path')
                                            ->label('Malaka toifasi')
                                            ->formatStateUsing(fn ($state) => $state ? 'Hujjatni ko\'rish' : 'Hujjat yuklanmagan')
                                            ->url(fn ($record) => $record->malaka_toifa_path &&
                                            Storage::disk('public')->exists($record->malaka_toifa_path) ?
                                                Storage::disk('public')->url($record->malaka_toifa_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge()
                                            ->color(fn ($state) => $state ? 'success' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-document-plus')
                                            ->visible(fn ($record) => $record->malaka_toifa_daraja !== 'mutaxasis')

                                    ])
                                    ->compact()
                                    ->columnSpan(1),

                                // Certificates Group
                                Section::make('Sertifikatlar')
                                    ->icon('heroicon-o-trophy')
                                    ->description('Milliy va xalqaro sertifikatlar')
                                    ->schema([
                                        TextEntry::make('milliy_sertifikat1_path')
                                            ->label('Milliy sertifikat #1')
                                            ->formatStateUsing(fn ($state) => $state ? 'Sertifikatni ko\'rish' : null)
                                            ->placeholder('Sertifikat yuklanmagan')
                                            ->url(fn ($record) => $record->milliy_sertifikat1_path ?
                                                Storage::disk('public')->url($record->milliy_sertifikat1_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-circle'),

                                        TextEntry::make('milliy_sertifikat2_path')
                                            ->label('Milliy sertifikat #2')
                                            ->formatStateUsing(fn ($state) => $state ? 'Sertifikatni ko\'rish' : null)
                                            ->placeholder('Sertifikat yuklanmagan')
                                            ->url(fn ($record) => $record->milliy_sertifikat2_path ?
                                                Storage::disk('public')->url($record->milliy_sertifikat2_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-circle'),

                                        TextEntry::make('xalqaro_sertifikat_path')
                                            ->label('Xalqaro sertifikat')
                                            ->formatStateUsing(fn ($state) => $state ? 'Sertifikatni ko\'rish' : null)
                                            ->placeholder('Sertifikat yuklanmagan')
                                            ->url(fn ($record) => $record->xalqaro_sertifikat_path ?
                                                Storage::disk('public')->url($record->xalqaro_sertifikat_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'warning' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-circle'),


                                    ])
                                    ->compact()
                                    ->columnSpan(1),

                                // Official Documents Group
                                Section::make('Ustamalar ro\'yxati')
                                    ->icon('heroicon-o-document-check')
                                    ->description('Ustama hujjatlari')
                                    ->schema([
                                        TextEntry::make('vazir_buyruq_path')
                                            ->label('Vazir ustamasi')
                                            ->formatStateUsing(fn ($state) => $state ? 'Hujjatni ko\'rish' : null)
                                            ->placeholder('Hujjat yuklanmagan')
                                            ->url(fn ($record) => $record->vazir_buyruq_path ?
                                                Storage::disk('public')->url($record->vazir_buyruq_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'danger' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-circle'),


                                        TextEntry::make('ustama_sertifikat_path')
                                            ->label('70% ustama sertifikat')
                                            ->formatStateUsing(fn ($state) => $state ? 'Sertifikatni ko\'rish' : null)
                                            ->placeholder('Sertifikat yuklanmagan')
                                            ->url(fn ($record) => $record->ustama_sertifikat_path ?
                                                Storage::disk('public')->url($record->ustama_sertifikat_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'info' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-circle'),
                                        // Add a placeholder for better balance if needed

                                        TextEntry::make('qoshimcha_ustama_path')
                                            ->label('Qo\'shimcha ustama hujjati')
                                            ->formatStateUsing(fn ($state) => $state ? 'Hujjatni ko\'rish' : null)
                                            ->placeholder('Hujjat yuklanmagan')
                                            ->url(fn ($record) => $record->qoshimcha_ustama_path ?
                                                Storage::disk('public')->url($record->qoshimcha_ustama_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'info' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-circle')
                                    ])
                                    ->compact()
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    // Helper Methods
    private static function calculateCompletion($record): int
    {
        $fields = [
            'passport_serial_number', 'passport_jshshir', 'passport_photo_path',
            'diplom_path', 'malaka_toifa_daraja', 'malaka_toifa_path',
            'milliy_sertifikat1_path', 'milliy_sertifikat2_path', 'vazir_buyruq_path',
            'ustama_sertifikat_path', 'xalqaro_sertifikat_path', 'malumotnoma_path',
            'profile_photo_path', 'telegram_id', 'signature_path'
        ];

        $completed = collect($fields)->filter(fn($field) => !empty($record->$field))->count();

        return round(($completed / count($fields)) * 100);
    }

    private static function getCompletionColor($record): string
    {
        $percentage = self::calculateCompletion($record);

        return match (true) {
            $percentage >= 80 => 'success',
            $percentage >= 50 => 'warning',
            default => 'danger'
        };
    }

    private static function countUploadedDocuments($record): int
    {
        $docs = [
            'passport_photo_path', 'diplom_path', 'malaka_toifa_path',
            'milliy_sertifikat1_path', 'milliy_sertifikat2_path', 'xalqaro_sertifikat_path',
            'profile_photo_path','malumotnoma_path', 'vazir_buyruq_path', 'ustama_sertifikat_path', 'signature_path'
        ];

        return collect($docs)->filter(fn($doc) => !empty($record->$doc))->count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Profile photo for visual identification
                Tables\Columns\ImageColumn::make('profile_photo_path')
                    ->label('Rasm')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(function () {
                        // Create a simple placeholder SVG or use a default image
                        return 'data:image/svg+xml;base64,' . base64_encode('
                        <svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                            <rect width="40" height="40" fill="#e5e7eb"/>
                            <path d="M20 12a4 4 0 100 8 4 4 0 000-8zM12 28a8 8 0 0116 0" stroke="#9ca3af" stroke-width="2" fill="none"/>
                        </svg>
                    ');
                    })
                    ->toggleable(),

                // Enhanced name column
                Tables\Columns\TextColumn::make('full_name')
                    ->label('F.I.SH')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable()
                    ->copyMessage('Ism nusxalandi!')
                    ->wrap()
                    ->size('md'),

                // Enhanced subjects with better display
                Tables\Columns\TextColumn::make('subjects.name')
                    ->label('Fanlar')
                    ->formatStateUsing(function ($record) {
                        $subjects = $record->subjects->pluck('name');
                        if ($subjects->isEmpty()) {
                            return 'Fan belgilanmagan';
                        }
                        if ($subjects->count() > 2) {
                            return $subjects->take(2)->implode(', ') . ' +' . ($subjects->count() - 2) . ' ta';
                        }
                        return $subjects->implode(', ');
                    })
                    ->searchable()
                    ->toggleable(),

                // Enhanced phone with better formatting
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Telefon nusxalandi!')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->placeholder('Telefon kiritilmagan')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'Kiritilmagan';
                        }
                        // Format phone number if needed
                        return $state;
                    })
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->toggleable(),

                // Enhanced qualification level with better colors
                Tables\Columns\TextColumn::make('malaka_toifa_daraja')
                    ->label('Malaka daraja')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'oliy-toifa' => 'success',
                        '1-toifa' => 'info',
                        '2-toifa' => 'warning',
                        'mutaxasis' => 'primary',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'oliy-toifa' => 'Oliy toifa',
                        '1-toifa' => '1-toifa',
                        '2-toifa' => '2-toifa',
                        'mutaxasis' => 'Mutaxasis',
                        default => $state ?: 'Belgilanmagan'
                    })
                    ->placeholder("Ma'lumot kiritilmagan")
                    ->sortable()
                    ->searchable(),

                // Telegram status as simple indicator
                Tables\Columns\TextColumn::make('telegram_id')
                    ->label('Telegram')
                    ->formatStateUsing(function ($state, $record) {
                        if (empty($state)) {
                            return 'Ulanmagan';
                        }
                        return "@{$state}";
                    })
                    ->badge()
                    ->color(function ($state) {
                        return empty($state) ? 'gray' : 'success';
                    })
                    ->icon(function ($state) {
                        return empty($state) ? 'heroicon-o-x-circle' : 'heroicon-o-chat-bubble-left-ellipsis';
                    })
                    ->copyable(fn ($state) => !empty($state))
                    ->copyMessage('Telegram username nusxalandi!')
                    ->tooltip(function ($record) {
                        return $record->telegram_id ?
                            "Telegram hisobi: @{$record->telegram_id}" :
                            'Telegram hisobi ulanmagan';
                    })
                    ->placeholder('Ma\'lumot kiritilmagan')
                    ->searchable()
                    ->toggleable(),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Enhanced region filter - only for superadmin
                Tables\Filters\SelectFilter::make('region_id')
                    ->label('Viloyat')
                    ->options(fn () => Region::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->placeholder('Viloyatni tanlang')
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereHas('maktab.district.region', function ($q) use ($data) {
                                $q->where('id', $data['value']);
                            });
                        }
                    })
                    ->visible(fn () => auth()->user()->role_id === 3),

                // Enhanced district filter - only for superadmin
                Tables\Filters\SelectFilter::make('district_id')
                    ->label('Tuman/Shahar')
                    ->options(fn () => District::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->placeholder('Tumanni tanlang')
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereHas('maktab.district', function ($q) use ($data) {
                                $q->where('id', $data['value']);
                            });
                        }
                    })
                    ->visible(fn () => auth()->user()->role_id === 3),

                // School filter - only for superadmin
                Tables\Filters\SelectFilter::make('maktab_id')
                    ->label('Maktab')
                    ->options(fn () => Maktab::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->placeholder('Maktabni tanlang')
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->where('maktab_id', $data['value']);
                        }
                    })
                    ->visible(fn () => auth()->user()->role_id === 3),

                // Enhanced qualification filter - visible for all
                Tables\Filters\SelectFilter::make('malaka_toifa_daraja')
                    ->label('Malaka daraja')
                    ->placeholder('Malaka darajasini tanlang')
                    ->options([
                        'oliy-toifa' => 'Oliy toifa',
                        'mutaxasis' => 'Mutaxasis',
                        '1-toifa' => 'Birinchi toifa',
                        '2-toifa' => 'Ikkinchi toifa',
                    ]),

                // Subject filter - visible for all
                Tables\Filters\SelectFilter::make('subjects')
                    ->label('Fan')
                    ->relationship('subjects', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Fanni tanlang'),

                // Telegram status filter - visible for all
                Tables\Filters\TernaryFilter::make('telegram_id')
                    ->label('Telegram holati')
                    ->placeholder('Hammasi')
                    ->trueLabel('Telegram ulangan')
                    ->falseLabel('Telegram ulanmagan')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('telegram_id'),
                        false: fn ($query) => $query->whereNull('telegram_id'),
                    ),
            ])

            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ko\'rish')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->label('Tahrirlash')
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('O\'chirish')
                    ->visible(fn () => auth()->user()->can('delete_teacher')),
                Tables\Actions\BulkAction::make('export')
                    ->label('Ma\'lumotlarni eksport qilish')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('format')
                            ->label('Fayl formati')
                            ->options([
                                'csv' => 'CSV (Excel uchun)',
                            ])
                            ->default('csv')
                            ->required()
                            ->helperText('Eksport qilinadigan fayl formatini tanlang'),

                        Forms\Components\CheckboxList::make('fields')
                            ->label('Eksport qilinadigan ma\'lumotlar')
                            ->options([
                                'basic' => 'Asosiy ma\'lumotlar (Ism, telefon, maktab)',
                                'documents' => 'Hujjatlar',
                                'subjects' => 'Fanlar',
                                'certificates' => 'Sertifikatlar',
                                'location' => 'Joylashuv (viloyat, tuman)',
                                'dates' => 'Sana ma\'lumotlari'
                            ])
                            ->default(['basic', 'documents', 'subjects'])
                            ->required()
                            ->columns(2)
                            ->helperText('Qaysi ma\'lumotlarni eksport qilishni tanlang'),

                        Forms\Components\Toggle::make('include_empty')
                            ->label('Bo\'sh maydonlarni ham ko\'rsatish')
                            ->default(true)
                            ->helperText('Bo\'sh maydonlar uchun "Kiritilmagan" yozuvini ko\'rsatish')
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Eksport parametrlarini belgilang')
                    ->modalDescription('Tanlangan o\'qituvchilar ma\'lumotlari belgilangan formatda eksport qilinadi.')
                    ->modalSubmitActionLabel('Eksport qilishni boshlash')
                    ->action(function ($records, array $data) {
                        return self::exportTeachersAdvanced($records, $data);
                    }),
            ])
            ->emptyStateHeading('O\'qituvchilar topilmadi')
            ->emptyStateDescription('Hech qanday o\'qituvchi ma\'lumotlari mavjud emas.')
            ->emptyStateIcon('heroicon-o-users')
            ->striped()
            ->paginated([25, 50, 100])
            ->extremePaginationLinks()
            ->poll('30s'); // Auto-refresh every 30 seconds
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'view' => Pages\ViewTeacher::route('/{record}'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }


    // These are all for exporting the teachers data

    private static function formatQualificationLevel(?string $level): string
    {
        return match($level) {
            'oliy-toifa' => 'Oliy toifa',
            '1-toifa' => 'Birinchi toifa',
            '2-toifa' => 'Ikkinchi toifa',
            'mutaxasis' => 'Mutaxasis',
            default => 'Belgilanmagan'
        };
    }

    private static function getDocumentStatus($path): string
    {
        return $path ? 'Mavjud' : 'Mavjud emas';
    }

    public static function exportTeachersAdvanced($records, array $options)
    {
        $format = $options['format'];
        $fields = $options['fields'];
        $includeEmpty = $options['include_empty'];

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "oqituvchilar_malumotlari_{$timestamp}.{$format}";

        switch ($format) {
            case 'csv':
                return self::exportToCsv($records, $fields, $includeEmpty, $filename);
            case 'xlsx':
                return self::exportToExcel($records, $fields, $includeEmpty, $filename);
            case 'pdf':
                return self::exportToPdf($records, $fields, $includeEmpty, $filename);
            default:
                return self::exportToCsv($records, $fields, $includeEmpty, $filename);
        }
    }

    private static function exportToCsv($records, $fields, $includeEmpty, $filename)
    {
        return response()->streamDownload(function () use ($records, $fields, $includeEmpty) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM qo'shish (Excel uchun)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Sarlavhalarni yaratish
            $headers = self::getExportHeaders($fields);
            fputcsv($file, $headers);

            // Ma'lumotlarni yozish
            foreach ($records as $record) {
                $row = self::getExportRow($record, $fields, $includeEmpty);
                fputcsv($file, $row);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private static function getExportHeaders($fields): array
    {
        $headers = [];

        if (in_array('basic', $fields)) {
            $headers = array_merge($headers, [
                'To\'liq ismi',
                'Telefon raqami',
                'Elektron pochta',
                'Maktab nomi',
                'Lavozim'
            ]);
        }

        if (in_array('subjects', $fields)) {
            $headers[] = 'O\'qitiladigan fanlar';
        }

        if (in_array('documents', $fields)) {
            $headers = array_merge($headers, [
                'Malaka daraja',
                'Pasport hujjati',
                'Diplom hujjati',
                'Malaka toifa hujjati '
            ]);
        }

        if (in_array('certificates', $fields)) {
            $headers = array_merge($headers, [
                'Milliy sertifikat #1',
                'Milliy sertifikat #2',
                'Xalqaro sertifikat',
                'Vazir ustamasi',
                '70% ustama sertifikat',
                'Qo\'shimcha ustama'
            ]);
        }

        if (in_array('location', $fields)) {
            $headers = array_merge($headers, ['Viloyat', 'Tuman/Shahar']);
        }

        if (in_array('dates', $fields)) {
            $headers[] = 'Ro\'yxatdan o\'tgan sana';
        }

        return $headers;
    }


    private static function getExportRow($record, $fields, $includeEmpty): array
    {
        $row = [];

        if (in_array('basic', $fields)) {
            $row = array_merge($row, [
                $record->full_name,
                $record->phone ?: ($includeEmpty ? 'Kiritilmagan' : ''),
                $record->user?->email ?: ($includeEmpty ? 'Elektron pochta mavjud emas' : ''),
                $record->maktab?->name ?: ($includeEmpty ? 'Maktab belgilanmagan' : ''),
                $record->lavozim ?: ($includeEmpty ? 'Lavozim belgilanmagan' : '')
            ]);
        }

        if (in_array('subjects', $fields)) {
            $subjects = $record->subjects->pluck('name')->implode(', ');
            $row[] = $subjects ?: ($includeEmpty ? 'Fan belgilanmagan' : '');
        }

        if (in_array('documents', $fields)) {
            // Handle malaka_toifa_path specially for mutaxasis
            $malakaToifaUrl = '';
            if ($record->malaka_toifa_daraja === 'mutaxasis') {
                $malakaToifaUrl = $includeEmpty ? 'Mutaxasis uchun talab qilinmaydi' : '';
            } else {
                $malakaToifaUrl = self::getDocumentUrl($record->malaka_toifa_path, $includeEmpty);
            }

            $row = array_merge($row, [
                self::formatQualificationLevel($record->malaka_toifa_daraja),
                self::getDocumentUrl($record->passport_photo_path, $includeEmpty),
                self::getDocumentUrl($record->diplom_path, $includeEmpty),
                $malakaToifaUrl
            ]);
        }


        if (in_array('certificates', $fields)) {
            $row = array_merge($row, [
                self::getDocumentUrl($record->milliy_sertifikat1_path, $includeEmpty),
                self::getDocumentUrl($record->milliy_sertifikat2_path, $includeEmpty),
                self::getDocumentUrl($record->xalqaro_sertifikat_path, $includeEmpty),
                self::getDocumentUrl($record->vazir_buyruq_path, $includeEmpty),
                self::getDocumentUrl($record->ustama_sertifikat_path, $includeEmpty),
                self::getDocumentUrl($record->qoshimcha_ustama_path, $includeEmpty)
            ]);
        }

        if (in_array('location', $fields)) {
            $row = array_merge($row, [
                $record->maktab?->district?->region?->name ?: ($includeEmpty ? 'Viloyat belgilanmagan' : ''),
                $record->maktab?->district?->name ?: ($includeEmpty ? 'Tuman belgilanmagan' : '')
            ]);
        }

        if (in_array('dates', $fields)) {
            $row[] = $record->created_at?->format('d.m.Y H:i') ?: ($includeEmpty ? 'Sana noma\'lum' : '');
        }

        return $row;
    }

    private static function getDocumentUrl(?string $path, bool $includeEmpty = true): string
    {
        if (empty($path)) {
            return $includeEmpty ? 'Hujjat mavjud emas' : '';
        }

        // Generate full URL for the document
        return url(Storage::disk('public')->url($path));
    }

    private static function exportToExcel($records, $fields, $includeEmpty, $filename)
    {
        // Excel eksport uchun Laravel Excel paketini o'rnatish kerak
        // Hozircha CSV kabi ishlaydi
        return self::exportToCsv($records, $fields, $includeEmpty, str_replace('.xlsx', '.csv', $filename));
    }

    private static function exportToPdf($records, $fields, $includeEmpty, $filename)
    {
        // PDF eksport uchun DomPDF yoki boshqa PDF kutubxonasi kerak
        // Hozircha CSV kabi ishlaydi
        return self::exportToCsv($records, $fields, $includeEmpty, str_replace('.pdf', '.csv', $filename));
    }


}
