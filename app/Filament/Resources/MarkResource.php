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
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;

class MarkResource extends Resource
{
    protected static ?string $model = Mark::class;
    protected static ?string $navigationLabel = "Baholar";
    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('exam_id')
                ->label('Imtihon tanlang')
                ->options(Exam::with(['sinf', 'subject'])->get()->mapWithKeys(fn ($exam) => [
                    $exam->id => "{$exam->sinf->name} - {$exam->subject->name}"
                ]))
                ->live()
                ->required()
                ->columnSpanFull(),

            Grid::make()
                ->schema(function (Get $get) {
                    $examId = $get('exam_id');

                    if (!$examId) return [];

                    $exam = Exam::with(['sinf.students', 'problems' => fn($q) => $q->orderBy('problem_number')])->find($examId);
                    if (!$exam) return [];

                    $students = $exam->sinf->students->sortBy('full_name');
                    $problems = $exam->problems;

                    $schema = [];

                    // Header row
                    $header = [
                        Placeholder::make('')->content("O'quvchi / Topshiriq"),
                    ];

                    foreach ($problems as $problem) {
                        $header[] = Placeholder::make('')
                            ->content("{$problem->problem_number}-topshiriq");
                    }

                    $schema[] = Grid::make(count($header))->schema($header);

                    // Students + Inputs
                    foreach ($students as $student) {
                        $row = [
                            Placeholder::make('')
                                ->content($student->full_name),
                        ];

                        foreach ($problems as $problem) {
                            $row[] = TextInput::make("marks[{$student->id}_{$problem->id}]")
                                ->hiddenLabel()
                                ->numeric()
                                ->minValue(0)
                                ->maxValue($problem->max_score ?? 10)
                                ->default(0);
                        }

                        $schema[] = Grid::make(count($row))->schema($row);
                    }

                    return $schema;
                })
                ->extraAttributes(['class' => 'mark-table'])
        ]);
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
                    ->label("O'quvchi")
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