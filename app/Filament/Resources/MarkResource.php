<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarkResource\Pages;
use App\Filament\Resources\MarkResource\RelationManagers;
use App\Models\Exam;
use App\Models\Mark;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\MarkResource\Pages\ViewMarks;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarkResource extends Resource
{
    protected static ?string $model = Mark::class;
    protected static ?string $navigationLabel = "Baholar";
    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    public static function form(Form $form): Form
    {
        return $form->schema(Mark::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Exam::query()->whereHas('marks'))
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Fan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Imtihon turi')
                    ->formatStateUsing(fn (Exam $record): string => $record->serial_number .'-'.$record->type)
                    ->sortable(),
                Tables\Columns\TextColumn::make('sinf.name')
                    ->label('Sinf')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Tahrirlash')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (Exam $record): string => MarkResource::getUrl('edit', ['record' => $record->marks()->first()->id]))
                    ->visible(fn (Exam $record): bool => $record->marks()->exists()),
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
        return array(
            'index' => Pages\ListMarks::route('/'),
            'create' => Pages\CreateMark::route('/create'),
            'edit' => Pages\EditMark::route('/{record}/edit'),
        );
    }
}
