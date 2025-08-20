<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SinfResource\Pages;
use App\Filament\Resources\SinfResource\RelationManagers;
use App\Models\Sinf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Gate;

class SinfResource extends Resource
{
    protected static ?string $model = Sinf::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function getModelLabel(): string
    {
        return "Sinf";
    }

    /**
     * @return string|null
     */
    public static function getPluralModelLabel(): string
    {
        return "Sinflar";
    }
    protected static ?string $navigationGroup = "O‘quv boshqaruvi";
    protected static ?string $navigationLabel = "Sinflar";

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Sinf::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label("Sinf ID"),
                Tables\Columns\TextColumn::make('name')
                    ->label("Sinf nomi")
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->icon('heroicon-o-pencil')->label("Tahrirlash"),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->icon('heroicon-o-trash')->label("O'chirish"),
                ])->label("Ko'proq"),
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
            'index' => Pages\ListSinfs::route('/'),
            'create' => Pages\CreateSinf::route('/create'),
//            'edit' => Pages\EditSinf::route('/{record}/edit'),
        ];
    }
}
