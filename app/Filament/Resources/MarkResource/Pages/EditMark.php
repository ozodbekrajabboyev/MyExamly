<?php

namespace App\Filament\Resources\MarkResource\Pages;

use App\Filament\Resources\MarkResource;
use App\Models\Exam;
use App\Models\Mark;
use Filament\Resources\Pages\EditRecord;

class EditMark extends EditRecord
{
    protected static string $resource = MarkResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Get the mark record and its associated exam
        $mark = $this->record;
        $exam = Exam::with(['sinf.students', 'problems'])->find($mark->exam_id);

        $data['exam_id'] = $exam->id;
        $data['marks'] = [];

        foreach ($exam->sinf->students as $student) {
            foreach ($exam->problems as $problem) {
                $existingMark = Mark::where('student_id', $student->id)
                    ->where('problem_id', $problem->id)
                    ->where('exam_id',   $exam->id)
                    ->first();

                $data['marks'][$student->id . '_' . $problem->id] = $existingMark->mark ?? 1;
            }
        }

        return $data;
    }

    protected function handleRecordUpdate($record, array $data): Mark
    {
//        dd($data);
        $data['exam_id'] = $record['exam_id'];
        $exam = Exam::with('problems')->find($data['exam_id']);

        if (isset($data['marks'])) {
            foreach ($data['marks'] as $key => $mark) {
                [$studentId, $problemId] = explode('_', $key);

                Mark::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'problem_id' => $problemId,
                        'exam_id' => $data['exam_id'],
                    ],
                    [
                        'mark' => $mark,
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
