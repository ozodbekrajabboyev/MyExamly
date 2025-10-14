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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MarkResource extends Resource
{
    protected static ?string $model = Mark::class;
    protected static ?string $navigationLabel = "Baholar";

    public static function getModelLabel(): string
    {
        return "Baho";
    }

    public static function getPluralModelLabel(): string
    {
        return "Baholar";
    }

    protected static ?string $navigationGroup = "Imtihonlar boshqaruvi";
    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    public static function form(Form $form): Form
    {
        return $form->schema(Mark::getForm());
    }

//    public static function getEloquentQuery(): Builder
//    {
//        $user = auth()->user();
//        return parent::getEloquentQuery()
//            ->when($user->role->name === 'teacher', function ($query) use ($user) {
//                $query->where('teacher_id', $user->teacher->id)
//                    ->orWhere('teacher2_id', $user->teacher->id);
//            });
//    }
    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = Auth::user();

                return Exam::query()
                    ->whereHas('marks') // only exams with marks
                    ->whereNotNull('problems') // only exams with problems
                    ->when($user->role->name === 'teacher', function ($query) use ($user) {
                        $query->where(function ($q) use ($user) {
                            $q->where('teacher_id', $user->teacher->id)
                                ->orWhere('teacher2_id', $user->teacher->id);
                        });
                    })
                    ->when($user->role->name !== 'superadmin', function ($query) use ($user) {
                        $query->where('maktab_id', $user->maktab_id);
                    });
            })
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Fan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Imtihon turi')
                    ->formatStateUsing(fn (Exam $record): string => $record->serial_number . '-' . $record->type)
                    ->sortable(),

                Tables\Columns\TextColumn::make('sinf.name')
                    ->label('Sinf')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Jarayonda',
                        'approved' => 'Tasdiqlangan',
                        'rejected' => 'Rad etilgan',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Jarayonda',
                        'approved' => 'Tasdiqlangan',
                        'rejected' => 'Rad etilgan',
                    ]),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Fan')
                    ->relationship('subject', 'name')
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Tahrirlash')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (Exam $record): string => MarkResource::getUrl('edit', [
                            'record' => \App\Models\Mark::where('exam_id', $record->id)->first()?->id,
                        ])
                    )
                    ->visible(fn (Exam $record): bool =>
                        $record->marks()->exists() &&
                        in_array(auth()->user()->role->name, ['superadmin', 'teacher'])
                    ),
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
