<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarkResource\Pages;
use App\Filament\Resources\MarkResource\RelationManagers;
use App\Models\Exam;
use App\Models\Mark;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class MarkResource extends Resource
{
    protected static ?string $model = Mark::class;
    protected static ?string $navigationLabel = "Baholar";
    protected static ?string $navigationIcon = 'heroicon-o-pencil';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('exam_id')
                    ->label('Imtihon tanlang')
                    ->options(Exam::with(['sinf', 'subject'])->get()->mapWithKeys(fn ($exam) => [
                        $exam->id => "{$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}"
                    ]))
                    ->live()
                    ->disabled(fn(string $operation): bool => $operation === 'edit')
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
                        $headerC = new HtmlString("<span class='text-green-500 font-bold text-l'>O'quvchi / Topshiriq</span>");
                        $header = [
                            Placeholder::make('')->content(fn () => $headerC),
                        ];

                        foreach ($problems as $problem) {
                            $header[] = Placeholder::make('')
                                ->content(new HtmlString("<span class='text-green-500 font-bold text-l'>{$problem->problem_number}-topshiriq (Max: {$problem->max_mark})</span>"));
                        }

                        $schema[] = Grid::make(count($header))->schema($header);

                        // Students + Inputs
                        foreach ($students as $student) {
                            $row = [
                                Placeholder::make('')
                                    ->content($student->full_name),
                            ];

                            foreach ($problems as $problem) {
                                $existingMark = Mark::where('student_id', $student->id)
                                    ->where('problem_id', $problem->id)
                                    ->first();

                                $row[] = TextInput::make("marks.{$student->id}_{$problem->id}")
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue($problem->max_score)
                                    ->default(1);
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
        return [
            'index' => Pages\ListMarks::route('/'),
            'create' => Pages\CreateMark::route('/create'),
            'edit' => Pages\EditMark::route('/{record}/edit'),
        ];
    }
}
