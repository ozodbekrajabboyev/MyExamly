<?php

namespace App\Filament\Resources\MarkResource\Pages;

use App\Filament\Resources\MarkResource;
use App\Models\Exam;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;

class ViewMarks extends Page
{
    protected static string $view = 'filament.pages.view-marks';

    public Exam $exam;

    public function getTitle(): string|Htmlable
    {
        return $this->exam->subject->name . ' - ' . $this->exam->type;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Sinf: ' . $this->exam->sinf->name;
    }

    public function mount(Exam $exam): void
    {
        $this->exam = $exam->load([
            'sinf.students',
            'problems',
            'marks' => fn($query) => $query->with(['student', 'problem'])
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Orqaga')
                ->url(MarkResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
