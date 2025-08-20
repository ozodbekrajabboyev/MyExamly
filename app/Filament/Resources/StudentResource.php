<?php

namespace App\Filament\Resources;

use App\Filament\Imports\StudentImporter;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Sinf;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    public static function getModelLabel(): string
    {
        return "O'quvchi";
    }

    /**
     * @return string|null
     */
    public static function getPluralModelLabel(): string
    {
        return "O'quvchilar";
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = "O‘quv boshqaruvi";
    protected static ?string $navigationLabel = "O'quvchilar";

    public static function getNavigationSort(): ?int
    {
        return 3;
    }
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
                    ->label("Sinf nomi")
                    ->formatStateUsing(fn (Student $record): string => $record->sinf->name .'-sinf')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sinf_nomi')
                    ->relationship('sinf', 'name')
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label("Tahrirlash"),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(StudentImporter::class)
                    ->icon('heroicon-o-arrow-down-tray'),
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
