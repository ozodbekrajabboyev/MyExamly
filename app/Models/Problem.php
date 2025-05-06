<?php

namespace App\Models;

use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
class Problem extends Model
{
    /** @use HasFactory<\Database\Factories\ProblemFactory> */
    use HasFactory;

    public function exam():BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
    public function marks():hasMany
    {
        return $this->hasMany(Mark::class);
    }


    public static function getForm(){
        return [
            Section::make('Yangi topshiriq yaratish')
                ->collapsible()
                ->columns(2)
                ->description("Yangi topshiriq yaratish uchun quyidagilarni to'ldiring!")
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Select::make('exam_id')
                        ->label('Imtihon nomi')
                        ->relationship('exam', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) =>
                        "{$record->sinf->name} | {$record->subject->name} | {$record->serial_number}-{$record->type}"
                        )
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('problem_number')
                        ->label('Topshiriq raqami')
                        ->required()
                        ->numeric(),
                    TextInput::make('max_mark')
                        ->label('Maximum ball')
                        ->required()
                        ->numeric(),
                ])
        ];
    }
}
