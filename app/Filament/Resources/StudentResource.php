<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Sinf;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = "O'quvchilar";

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return "O'quvchilar soni";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Student::getForm());
    }


    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(function ($action){
                return $action->button()->label('Filtrlar');
            })
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label("O'quvchining IFSH")
                    ->searchable(),
                Tables\Columns\TextColumn::make('sinf.name')
                    ->label("Sinfi")
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sinf_nomi')
                    ->relationship('sinf', 'name')
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label("Tahrirlash"),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
//            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
