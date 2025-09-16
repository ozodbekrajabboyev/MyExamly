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

    public static function getNavigationBadgeTooltip(): ?string
    {
        return "O'qituvchilar soni";
    }

    public static function getModelLabel(): string
    {
        return "O'qituvchi";
    }

    /**
     * @return string
     */
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
                Section::make()
                    ->schema([
                        Split::make([
                            // Left Column - Primary Information
                            Group::make([
                                // Personal Details Card
                                Section::make('Shaxsiy Ma\'lumotlar')
                                    ->icon('heroicon-o-user')
                                    ->description('Asosiy shaxsiy va kasbiy ma\'lumotlar')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('full_name')
                                                    ->label('To\'liq IFSH')
                                                    ->size(TextEntry\TextEntrySize::Large)
                                                    ->weight(FontWeight::Bold)
                                                    ->color('primary'),

                                                TextEntry::make('maktab.name')
                                                    ->label('Maktab')
                                                    ->icon('heroicon-o-building-office-2')
                                                    ->badge()
                                                    ->color('success'),

                                                TextEntry::make('user.email')
                                                    ->label('Elektron Pochta')
                                                    ->icon('heroicon-o-envelope')
                                                    ->copyable()
                                                    ->copyMessage('Email nusxalandi!')
                                                    ->color('gray'),

                                                TextEntry::make('phone')
                                                    ->label('Telefon Raqami')
                                                    ->icon('heroicon-o-phone')
                                                    ->copyable()
                                                    ->copyMessage('Telefon nusxalandi!')
                                                    ->placeholder('Kiritilmagan')
                                                    ->color('gray'),
                                            ]),
                                    ])
                                    ->compact(),

                                // Contact & Social
                                Section::make('📱 Aloqa Ma\'lumotlari')
                                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('telegram_id')
                                                    ->label('Telegram')
                                                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                                    ->copyable()
                                                    ->badge()
                                                    ->color('info')
                                                    ->placeholder('Ulanmagan'),

                                                TextEntry::make('created_at')
                                                    ->label('Ro\'yxatdan O\'tgan')
                                                    ->icon('heroicon-o-calendar')
                                                    ->dateTime('d.m.Y')
                                                    ->badge()
                                                    ->color('warning'),
                                            ]),
                                    ])
                                    ->compact(),
                            ])->grow(),
                        ])->from('lg'),
                    ])
                    ->compact(),

                // Identification Section with Passport Photo
                Section::make('Shaxsni Tasdiqlovchi Hujjatlar')
                    ->icon('heroicon-o-identification')
                    ->description('Rasmiy shaxsni tasdiqlovchi hujjatlar va pasport ma\'lumotlari')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // Passport Photo
                                Group::make([
                                    ImageEntry::make('passport_photo_path')
                                        ->label('📷 Pasport Rasmi')
                                        ->disk('public')
                                        ->height(200)
                                        ->width('100%')
                                        ->extraAttributes([
                                            'class' => 'rounded-lg shadow-md border-2 border-gray-100'
                                        ])
                                        ->placeholder('📸 Rasm yuklanmagan'),

                                    TextEntry::make('passport_photo_path')
                                        ->label('Rasmni Yuklash')
                                        ->formatStateUsing(fn ($state) => $state ? 'Rasmni Yuklash' : 'Yuklanmagan')
                                        ->url(fn ($record) => $record->passport_photo_path ?
                                            Storage::disk('public')->url($record->passport_photo_path) : null)
                                        ->openUrlInNewTab()
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'success' : 'gray')
                                        ->icon('heroicon-o-arrow-down-tray'),
                                ])->columnSpan(1),

                                // Passport Information
                                Group::make([
                                    TextEntry::make('passport_serial_number')
                                        ->label('Pasport Seriyasi')
                                        ->icon('heroicon-o-hashtag')
                                        ->copyable()
                                        ->badge()
                                        ->color('primary')
                                        ->placeholder('Kiritilmagan'),

                                    TextEntry::make('passport_jshshir')
                                        ->label('JSHSHIR')
                                        ->icon('heroicon-o-finger-print')
                                        ->copyable()
                                        ->badge()
                                        ->color('secondary')
                                        ->placeholder('Kiritilmagan'),
                                    TextEntry::make('signature_path')
                                        ->label('Imzo Holati')
                                        ->formatStateUsing(fn ($state) => $state ? 'Elektron imzo mavjud' : 'Elektron imzo yuklanmagan')
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'success' : 'warning')
                                        ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),

                                    TextEntry::make('signature_path')
                                        ->label('Imzoni Ko\'rish')
                                        ->formatStateUsing(fn ($state) => $state ? 'Imzoni Yuklash' : 'Mavjud Emas')
                                        ->url(fn ($record) => $record->teacher && $record->teacher->signature_path ?
                                            Storage::disk('public')->url($record->teacher->signature_path) : null)
                                        ->openUrlInNewTab()
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'info' : 'gray')
                                        ->icon('heroicon-o-arrow-down-tray')
                                        ->visible(fn ($record) => $record->user && $record->user->signature_path),
                                ])->columnSpan(2),

                            ]),
                    ])
                    ->collapsible(),

                // Professional Documents
                Section::make('Kasbiy Hujjatlar')
                    ->icon('heroicon-o-document-text')
                    ->description('Ta\'lim sertifikatlari va kasbiy hujjatlar')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // Educational Documents Group
                                Group::make([
                                    TextEntry::make('diplom_path')
                                        ->label('🎓 Diplom')
                                        ->formatStateUsing(fn ($state) => $state ? 'Hujjatni Yuklash' : 'Yuklanmagan')
                                        ->url(fn ($record) => $record->diplom_path ?
                                            Storage::disk('public')->url($record->diplom_path) : null)
                                        ->openUrlInNewTab()
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'success' : 'gray')
                                        ->icon('heroicon-o-arrow-down-tray'),

                                    TextEntry::make('malaka_toifa_path')
                                        ->label('📜 Malaka Toifasi')
                                        ->formatStateUsing(fn ($state) => $state ? 'Hujjatni Yuklash' : 'Yuklanmagan')
                                        ->url(fn ($record) => $record->malaka_toifa_path ?
                                            Storage::disk('public')->url($record->malaka_toifa_path) : null)
                                        ->openUrlInNewTab()
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'success' : 'gray')
                                        ->icon('heroicon-o-arrow-down-tray'),
                                ])->columnSpan(1),

                                // Certificates Group
                                Group::make([
                                    TextEntry::make('milliy_sertifikat_path')
                                        ->label('🏆 Milliy Sertifikat')
                                        ->formatStateUsing(fn ($state) => $state ? 'Sertifikatni Yuklash' : 'Yuklanmagan')
                                        ->url(fn ($record) => $record->milliy_sertifikat_path ?
                                            Storage::disk('public')->url($record->milliy_sertifikat_path) : null)
                                        ->openUrlInNewTab()
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'success' : 'gray')
                                        ->icon('heroicon-o-arrow-down-tray'),

                                    TextEntry::make('xalqaro_sertifikat_path')
                                        ->label('🌍 Xalqaro Sertifikat')
                                        ->formatStateUsing(fn ($state) => $state ? 'Sertifikatni Yuklash' : 'Yuklanmagan')
                                        ->url(fn ($record) => $record->xalqaro_sertifikat_path ?
                                            Storage::disk('public')->url($record->xalqaro_sertifikat_path) : null)
                                        ->openUrlInNewTab()
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'success' : 'gray')
                                        ->icon('heroicon-o-arrow-down-tray'),
                                ])->columnSpan(1),

                                // Reference Documents Group
                                Group::make([
                                    TextEntry::make('malumotnoma_path')
                                        ->label('📋 Ma\'lumotnoma')
                                        ->formatStateUsing(fn ($state) => $state ? 'Hujjatni Yuklash' : 'Yuklanmagan')
                                        ->url(fn ($record) => $record->malumotnoma_path ?
                                            Storage::disk('public')->url($record->malumotnoma_path) : null)
                                        ->openUrlInNewTab()
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'success' : 'gray')
                                        ->icon('heroicon-o-arrow-down-tray'),
                                ])->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

            ]);
    }

// Helper Methods
    private static function calculateCompletion($record): int
    {
        $fields = [
            'passport_serial_number', 'passport_jshshir', 'passport_photo_path',
            'diplom_path', 'malaka_toifa_path', 'milliy_sertifikat_path',
            'xalqaro_sertifikat_path', 'malumotnoma_path', 'telegram_id'
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
            'milliy_sertifikat_path', 'xalqaro_sertifikat_path', 'malumotnoma_path'
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
