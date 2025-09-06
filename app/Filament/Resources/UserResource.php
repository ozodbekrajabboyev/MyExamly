<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getModelLabel(): string
    {
        return "Foydalanuvchi";
    }

    /**
     * @return string|null
     */
    public static function getPluralModelLabel(): string
    {
        return "Foydalanuvchilar";
    }
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = "Foydalanuvchilar";

    protected static ?string $navigationGroup = 'Foydalanuvchilar boshqaruvi';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Foydalanuvchilar soni';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(User::get_form());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label("IFSH")
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label("Email")
                    ->searchable(),
                Tables\Columns\TextColumn::make('maktab.name')
                    ->label("Maktab")
                    ->searchable(),
                TextColumn::make('role.name')
                    ->label("Role")
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'teacher' => 'gray',
                        'admin' => 'warning',
                        'superadmin' => 'success'
                    }),
                ImageColumn::make('signature_path')
                    ->label('Imzo')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/placeholder-signature.png')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->label('Tahrirlash'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
//            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
