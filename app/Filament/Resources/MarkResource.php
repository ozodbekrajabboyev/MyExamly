<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarkResource\Pages;
use App\Filament\Resources\MarkResource\RelationManagers;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Sinf;
use App\Models\Student;
use App\Models\Problem;
use Filament\Forms;
use Filament\Forms\Form;
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
        return $form
            ->schema(Mark::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exam.subject.name')
                    ->label('Fan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('exam.type')
                    ->label('Imtihon turi')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sinf.name')
                    ->label('Sinf')
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('O\'quvchi')
                    ->sortable(),

                Tables\Columns\TextColumn::make('problem.problem_number')
                    ->label('Topshiriq')
                    ->formatStateUsing(fn ($state) => $state . "-topshiriq")
                    ->sortable(),

                Tables\Columns\TextColumn::make('mark')
                    ->label('Ball')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label("Tahrirlash"),
            ])
            ->searchable()
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
            'index' => Pages\ListMarks::route('/'),
            'create' => Pages\CreateMark::route('/create'),
            'edit' => Pages\EditMark::route('/{record}/edit'),
        ];
    }
}
