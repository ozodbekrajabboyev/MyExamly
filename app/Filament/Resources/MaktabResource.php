<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaktabResource\Pages;
use App\Models\Maktab;
use App\Models\Region;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MaktabResource extends Resource
{
    protected static ?string $model = Maktab::class;

    protected static ?string $navigationLabel = "Maktablar";
    protected static ?string $navigationGroup = 'Foydalanuvchilar boshqaruvi';
    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function getModelLabel(): string
    {
        return "Maktab";
    }

    public static function getPluralModelLabel(): string
    {
        return "Maktablar";
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return "Maktablar soni";
    }

    // ✅ FORM
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('region_id')
                ->label('Viloyat')
                ->options(Region::pluck('name', 'id')->toArray())
                ->searchable()
                ->reactive()
                ->required(),

            Forms\Components\Select::make('district_id')
                ->label('Tuman/Shahar')
                ->options(function (callable $get) {
                    $regionId = $get('region_id');
                    if (!$regionId) {
                        return District::pluck('name', 'id');
                    }
                    return District::where('region_id', $regionId)->pluck('name', 'id');
                })
                ->searchable()
                ->required()
                ->reactive(),

            Forms\Components\TextInput::make('name')
                ->label('Maktab nomi')
                ->required(),
        ]);
    }

    // ✅ TABLE
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Maktab')
                    ->searchable(),

                Tables\Columns\TextColumn::make('region.name')
                    ->label('Viloyat')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('district.name')
                    ->label('Tuman/Shahar')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('region_id')
                    ->label('Viloyat')
                    ->options(Region::pluck('name', 'id')->toArray())
                    ->searchable(),

                Tables\Filters\SelectFilter::make('district_id')
                    ->label('Tuman/Shahar')
                    ->options(District::pluck('name', 'id')->toArray())
                    ->searchable(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaktabs::route('/'),
            'create' => Pages\CreateMaktab::route('/create'),
            'edit' => Pages\EditMaktab::route('/{record}/edit'),
        ];
    }
}
