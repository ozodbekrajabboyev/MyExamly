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
                                            ->size(TextEntry\TextEntrySize::Large)
                                            ->weight(FontWeight::Bold)
                                            ->color('primary')
                                            ->icon('heroicon-o-user-circle'),

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
                                            ->formatStateUsing(fn ($state) => $state ? "@{$state}" : 'ID kiritilmagan'),

                                        TextEntry::make('created_at')
                                            ->label('Ro\'yxatdan o\'tgan sana')
                                            ->icon('heroicon-o-calendar-days')
                                            ->dateTime('d.m.Y H:i')
                                            ->badge()
                                            ->color('amber')
                                            ->weight('medium'),

                                        TextEntry::make('updated_at')
                                            ->label('So\'ngi yangilanish')
                                            ->icon('heroicon-o-clock')
                                            ->dateTime('d.m.Y H:i')
                                            ->badge()
                                            ->color('gray')
                                            ->since()
                                            ->placeholder('Hech qachon yangilanmagan'),
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

                                // Digital Signature Section
                                Section::make('Shaxsiy imzo')
                                    ->icon('heroicon-o-pencil-square')
                                    ->description('Shaxsiy imzo va tasdiqlash')
                                    ->schema([
                                        TextEntry::make('signature_status')
                                            ->default("Shaxsiy imzo mavjud emas")
                                            ->label('Imzo holati')
                                            ->formatStateUsing(fn ($state, $record) =>
                                            $record->signature_path ? 'Imzo mavjud' : 'Imzo yuklanmagan')
                                            ->badge()
                                            ->color(fn ($state, $record) => $record->signature_path ? 'success' : 'warning')
                                            ->icon(fn ($state, $record) =>
                                            $record->signature_path ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle'),

                                        TextEntry::make('signature_path')
                                            ->label('Imzoni ko\'rish')
                                            ->formatStateUsing(fn ($state) => $state ? 'Imzoni ochish' : null)
                                            ->placeholder('Imzo mavjud emas')
                                            ->url(fn ($record) => $record->signature_path ?
                                                Storage::disk('public')->url($record->signature_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'info' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-circle')
                                            ->visible(fn ($record) => $record->signature_path),
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
                                            ->formatStateUsing(fn ($state) => $state ? 'Hujjatni ko\'rish' : null)
                                            ->placeholder('Hujjat yuklanmagan')
                                            ->url(fn ($record) => $record->malaka_toifa_path ?
                                                Storage::disk('public')->url($record->malaka_toifa_path) : null)
                                            ->openUrlInNewTab()
                                            ->badge(fn ($state) => !empty($state))
                                            ->color(fn ($state) => $state ? 'success' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-circle'),
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
                                        TextEntry::make('placeholder')
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
            'telegram_id', 'signature_path'
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
            'malumotnoma_path', 'vazir_buyruq_path', 'ustama_sertifikat_path', 'signature_path'
        ];

        return collect($docs)->filter(fn($doc) => !empty($record->$doc))->count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('F.I.Sh')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Foydalanuvchi')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subjects.name')
                    ->label("Fanlar")
                    ->formatStateUsing(fn ($state, $record) => $record->subjects->pluck('name')->implode(', ')),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),

                Tables\Columns\TextColumn::make('malaka_toifa_daraja')
                    ->label('Malaka daraja')
                    ->badge()
                    ->color('primary'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('region_id')
                    ->label('Viloyat')
                    ->options(fn () => Region::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereHas('maktab.district.region', function ($q) use ($data) {
                                $q->where('id', $data['value']);
                            });
                        }
                    }),

                Tables\Filters\SelectFilter::make('district_id')
                    ->label('Tuman/Shahar')
                    ->options(fn () => District::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereHas('maktab.district', function ($q) use ($data) {
                                $q->where('id', $data['value']);
                            });
                        }
                    }),

                Tables\Filters\SelectFilter::make('maktab_id')
                    ->label('Maktab')
                    ->options(fn () => Maktab::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->where('maktab_id', $data['value']);
                        }
                    }),

                Tables\Filters\SelectFilter::make('malaka_toifa_daraja')
                    ->label('Malaka Toifa')
                    ->options([
                        'oliy-toifa' => 'Oliy toifa',
                        'mutaxasis' => 'Mutaxasis',
                        '1-toifa' => '1-toifa',
                        '2-toifa' => '2-toifa',
                    ]),
            ]);
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
        ];
    }
}
