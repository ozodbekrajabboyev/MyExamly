<?php

namespace App\Filament\Resources\MarkResource\Pages;

use App\Filament\Resources\MarkResource;
use App\Models\Exam;
use App\Services\MarkService;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;

class ViewMarks extends Page
{
    protected static string $resource = MarkResource::class;
    protected static string $view = 'filament.resources.mark-resource.pages.view-marks';

    public ?array $data = [];
    public ?Exam $selectedExam = null;
    public ?array $reportData = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('exam_id')
                    ->label('Imtihonni tanlang')
                    ->options(function () {
                        $user = auth()->user();

                        $query = Exam::query()
                            ->whereHas('marks') // Only exams with marks
                            ->where('maktab_id', $user->maktab_id)
                            ->with(['sinf', 'subject']);

                        if ($user->role->name === 'teacher') {
                            $query->where('teacher_id', $user->teacher->id);
                        }

                        return $query->get()
                            ->mapWithKeys(function ($exam) {
                                $label = "{$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}";
                                return [$exam->id => $label];
                            });
                    })
                    ->live()
                    ->afterStateUpdated(function (Get $get, $state) {
                        if ($state) {
                            $this->loadExamReport($state);
                        } else {
                            $this->selectedExam = null;
                            $this->reportData = null;
                        }
                    })
                    ->columnSpanFull()
            ])
            ->statePath('data');
    }

    protected function loadExamReport(int $examId): void
    {
        $this->selectedExam = Exam::with(['sinf.students', 'subject', 'teacher'])->find($examId);

        if ($this->selectedExam) {
            $markService = new MarkService();
            $this->reportData = $markService->generateExamReport($this->selectedExam);
        }
    }

    public function getTitle(): string
    {
        return 'Baho hisoboti';
    }

    protected function getViewData(): array
    {
        return [
            'selectedExam' => $this->selectedExam,
            'reportData' => $this->reportData,
        ];
    }
}
