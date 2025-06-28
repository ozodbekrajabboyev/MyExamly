<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaktabResource\Pages;
use App\Filament\Resources\MaktabResource\RelationManagers;
use App\Models\Maktab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaktabResource extends Resource
{
    protected static ?string $model = Maktab::class;

    protected static ?string $navigationLabel = "Maktablar";
    protected static ?string $navigationGroup = 'Foydalanuvchilar boshqaruvi';

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return "Maktablar soni";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMaktabs::route('/'),
            'create' => Pages\CreateMaktab::route('/create'),
            'edit' => Pages\EditMaktab::route('/{record}/edit'),
        ];
    }
}
