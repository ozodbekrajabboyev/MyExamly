<?php

namespace App\Filament\Resources\MarkResource\Pages;

use App\Models\Exam;
use App\Filament\Resources\MarkResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMark extends CreateRecord
{
    protected static string $resource = MarkResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // dd($data);
        $exam = Exam::with('sinf')->find($data['exam_id']);
        
        if (!$exam) {
            Notification::make()
                ->title('Xatolik')
                ->body('Imtihon topilmadi!')
                ->danger()
                ->send();
            throw new \Exception("Exam not found");
        }

        if (!$exam->sinf) {
            Notification::make()
                ->title('Xatolik')
                ->body('Imtihon sinf bilan boglanmagan!')
                ->danger()
                ->send();
            throw new \Exception("Exam has no associated class");
        }

        $createdMarks = [];
        $errors = [];

        // dd($data['marks']);

        foreach ($data ?? [] as $key => $score) {
            try {
                $cleanKey = str_replace(['marks[', ']'], '', $key);
                [$studentId, $problemId] = explode('_', $cleanKey);
                
                $mark = \App\Models\Mark::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'problem_id' => $problemId,
                        'exam_id' => $exam->id,
                    ],
                    [
                        'mark' => $score,
                        'sinf_id' => $exam->sinf_id
                    ]
                );
                
                $createdMarks[] = $mark;
            } catch (\Exception $e) {
                $errors[] = "O'quvchi ID {$studentId}, topshiriq ID {$problemId}: " . $e->getMessage();
            }
        }
        // dd($createdMarks);

        if (!empty($errors)) {
            Notification::make()
                ->title("Ba'zi baholarda xatolik")
                ->body(implode('\n', array_slice($errors, 0, 3)))
                ->danger()
                ->send();
        }


        return $createdMarks[0] ?? new \App\Models\Mark();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', ['exam_id' => $this->data['exam_id']]);
    }
}