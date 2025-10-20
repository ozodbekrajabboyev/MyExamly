<?php

namespace App\Filament\Resources;

use App\Filament\Exports\StudentExporter;
use App\Filament\Imports\StudentImporter;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Sinf;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
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
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-user')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('sinf.name')
                    ->label("Sinf")
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label("Qo'shilgan sana")
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('sinf_id')
                    ->label('Sinf')
                    ->relationship('sinf', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Sinfni tanlang'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dan'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Gacha'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->label("Qo'shilgan sana"),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Tahrirlash')
                    ->icon('heroicon-m-pencil-square')
                    ->modal()
                    ->modalHeading(fn ($record) => "O'quvchini tahrirlash: " . $record->full_name)
                    ->modalWidth('lg'),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(StudentImporter::class)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('O\'quvchilarni import qilish'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->label("O'chirish"),
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(StudentExporter::class)
                        ->icon('heroicon-o-arrow-up-tray')
                        ->label('Exportlash'),
                ])->label("Ko'proq"),
            ])
            ->emptyStateHeading("O'quvchilar topilmadi")
            ->emptyStateDescription("Hozircha hech qanday o'quvchi qo'shilmagan")
            ->emptyStateIcon('heroicon-o-user-group');
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
            'edit' => Pages\EditStudent::route('/{record}/edit')
        ];
    }
}
