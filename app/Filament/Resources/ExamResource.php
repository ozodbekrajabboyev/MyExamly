<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Models\Exam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationLabel = "Imtihonlar";

    public static function getModelLabel(): string
    {
        return "Imtihon";
    }

    /**
     * @return string|null
     */
    public static function getPluralModelLabel(): string
    {
        return "Imtihonlar";
    }

    protected static ?string $navigationGroup = "Imtihonlar boshqaruvi";
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Exam::getForm());
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        // If teacher, restrict to their own exams or if it is an admin restrict to their own school's exam
        if ($user->role->name === 'teacher') {
            return parent::getEloquentQuery()
                ->where(function ($query) use ($user) {
                    $query->where('teacher_id', $user->teacher->id)
                        ->orWhere('teacher2_id', $user->teacher->id);
                });

        }else if($user->role->name === 'admin'){
            return parent::getEloquentQuery()
                ->where('maktab_id', $user->maktab_id);
        }

        // If superadmin, show all
        return parent::getEloquentQuery();
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sinf.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Fan nomi')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teacher.full_name')
                    ->label("O'qituvchining nomi")
                    ->formatStateUsing(function ($record) {
                        $teachers = [$record->teacher->full_name];

                        if ($record->teacher2) {
                            $teachers[] = $record->teacher2->full_name;
                        }

                        return implode(', ', $teachers);
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Imtihon nomi')
                    ->formatStateUsing(fn ($state, $record) => "{$record->serial_number} - {$record->type}")
                    ->searchable(['type', 'serial_number']) // Search both fields
                    ->sortable(),
//                Tables\Columns\TextColumn::make('type')
//                    ->label('Imtihon turi')
//                    ->searchable(),
//                Tables\Columns\TextColumn::make('serial_number')
//                    ->label('Tartib raqam')
//                    ->numeric()
//                    ->sortable(),
                Tables\Columns\TextColumn::make('problems_count')
                    ->label('Topshiriqlar soni')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('metod.id')
                    ->label('Metodbirlashma rahbari')
                    ->formatStateUsing(function ($state) {
                        $teacher = \App\Models\Teacher::find($state);
                        return $teacher
                            ? "{$teacher->full_name}"
                            : "#{$state} - Noma'lum";
                    })
                    ->sortable(),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(""),
                Tables\Actions\Action::make('manage_marks')
                    ->label('')
                    ->icon('heroicon-o-table-cells')
                    ->url(fn (Exam $record): string => "/exams/manage-marks?exam_id={$record->id}")
                    ->visible(fn (Exam $record): bool => $record->sinf->students()->count() > 0)
                    ->color('success'),
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
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
            'manage-marks' => Pages\ManageMarks::route('/manage-marks'),
        ];
    }
}
