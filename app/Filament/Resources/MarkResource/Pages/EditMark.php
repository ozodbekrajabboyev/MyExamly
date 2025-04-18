<?php

namespace App\Filament\Resources\MarkResource\Pages;

use App\Filament\Resources\MarkResource;
use App\Models\Exam;
use App\Models\Mark;
use Filament\Resources\Pages\EditRecord;

class EditMark extends EditRecord
{
    protected static string $resource = MarkResource::class;

//    protected function resolveRecord(int | string $key): Mark
//    {
//        // Bu yerda exam_id bo'yicha birinchi mark recordini qaytaramiz
//        // Aslida bu record faqat sahifani ochish uchun ishlatiladi
//        return Mark::where('exam_id', $key)->firstOrFail();
//    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $exam = Exam::with(['sinf.students', 'problems', 'marks'])->findOrFail($this->record->id);

        $data['exam_id'] = $exam->id;
        $data['marks'] = []; // Massivni tozalab olamiz

        // Barcha o'quvchilar va topshiriqlar uchun default qiymatlarni to'ldiramiz
        foreach ($exam->sinf->students as $student) {
            foreach ($exam->problems as $problem) {
                // Avvalgi bahoni topamiz yoki default 1 qo'yamiz
                $existingMark = $exam->marks
                    ->where('student_id', $student->id)
                    ->where('problem_id', $problem->id)
                    ->first();

                $data['marks'][$student->id.'_'.$problem->id] = $existingMark->mark ?? 1;
            }
        }

//        dd($data);
        return $data;
    }

    protected function handleRecordUpdate($record, array $data): Mark
    {
        $exam = Exam::with('problems')->find($data['exam_id']);

        if (isset($data['marks'])) {
            foreach ($data['marks'] as $key => $mark) {
                [$studentId, $problemId] = explode('_', $key);

                Mark::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'problem_id' => $problemId,
                    ],
                    [
                        'mark' => $mark,
                        'exam_id' => $data['exam_id'],
                    ]
                );
            }
        }

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
