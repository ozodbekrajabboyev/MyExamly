<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemResource\Pages;
use App\Filament\Resources\ProblemResource\RelationManagers;
use App\Models\Exam;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Problem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProblemResource extends Resource
{
    protected static ?string $model = Problem::class;

    protected static ?string $navigationLabel = "Topshiriqlar";
    protected static ?string $navigationGroup = "Imtihonlar boshqaruvi";
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Problem::getForm());
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->when($user->role->name === 'teacher', function ($query) use ($user) {
                $query->whereHas('exam', function ($q) use ($user) {
                    $q->where('teacher_id', $user->teacher->id);
                });
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exam.id')
                    ->label('Imtihon nomi')
                    ->formatStateUsing(function ($state, $record) {
                        $exam = $record->exam; // Exam modeliga relation orqali kirish
                        return $exam ? "{$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type} " : '-';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('problem_number')
                    ->label('Topshiriq raqami')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state."-topshiriq"),
                Tables\Columns\TextColumn::make('max_mark')
                    ->label('Maximum ball')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('exam_id')
                    ->label('Imtihon bo\'yicha filtrlash')
                    ->options(function () {
                        $user = auth()->user();
                        $query = Exam::query()->with(['sinf', 'subject']);

                        if ($user->role->name === 'teacher') {
                            $query->where('teacher_id', $user->teacher->id);
                        }

                        return $query->get()
                            ->mapWithKeys(function ($exam) {
                                $label = "{$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}";
                                return [$exam->id => $label];
                            });
                    })
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->where('exam_id', $data['value']);
                        }
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make()->label("Tahrirlash"),
            ])
            ->searchable()
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->label("O'chirish")
                        ->visible(fn () => auth()->user()->role_id != 2), // faqat roli 2 bo‘lmaganlarga
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
            'index' => Pages\ListProblems::route('/'),
            'create' => Pages\CreateProblem::route('/create'),
            'edit' => Pages\EditProblem::route('/{record}/edit'),
        ];
    }
}
