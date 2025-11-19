<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Services\MarkService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExam extends CreateRecord
{
    protected static string $resource = ExamResource::class;

    protected function afterCreate(): void
    {
        $exam = $this->record;

        // Use MarkService to auto-create marks
        $markService = new MarkService();
        $markService->createMarksForExam($exam);
    }

    protected function getRedirectUrl(): string
    {
        return "/exams";
    }
}
