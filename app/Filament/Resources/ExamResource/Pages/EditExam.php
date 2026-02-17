<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExam extends EditRecord
{
    protected static string $resource = ExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();

        if ($user->role->name === 'admin') {
            $currentRecord = $this->getRecord();

            // Preserve critical fields that affect uniqueness
            if (!isset($data['sinf_id'])) {
                $data['sinf_id'] = $currentRecord->sinf_id;
            }
            if (!isset($data['subject_id'])) {
                $data['subject_id'] = $currentRecord->subject_id;
            }
            if (!isset($data['serial_number'])) {
                $data['serial_number'] = $currentRecord->serial_number;
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): ?string
    {
        return "/";
    }
}
