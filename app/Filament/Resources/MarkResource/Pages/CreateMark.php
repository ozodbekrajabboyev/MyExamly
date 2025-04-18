<?php

namespace App\Filament\Resources\MarkResource\Pages;

use App\Filament\Resources\MarkResource;
use App\Models\Exam;
use App\Models\Mark;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateMark extends CreateRecord
{
    protected static string $resource = MarkResource::class;

    protected function handleRecordCreation(array $data): Model
    {
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

        unset($data['exam_id']);

        foreach ($data ?? [] as $key => $score) {
            try {
                $cleanKey = str_replace(['marks[', ']'], '', $key);

                [$studentId, $problemId] = explode('_', $cleanKey);

                $mark = Mark::updateOrCreate(
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
//                dd($createdMarks);
                $errors[] = "O'quvchi ID, topshiriq ID: " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            Notification::make()
                ->title("Ba'zi baholarda xatolik")
                ->body(implode('\n', array_slice($errors, 0, 3)))
                ->danger()
                ->send();
        }

//        dd($createdMarks[0]);
        return $createdMarks[0] ?? new \App\Models\Mark();
    }

}
