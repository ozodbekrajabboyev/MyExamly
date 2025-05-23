<?php

namespace App\Filament\Resources\MarkResource\Pages;

use App\Filament\Resources\MarkResource;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

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

    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
//        dd($data);
        $data['exam_id'] = $record['exam_id'];
        $exam = Exam::with('problems')->find($data['exam_id']);

        if (isset($data['marks'])) {
            $errors = [];
            foreach ($data['marks'] as $key => $mark) {
                [$studentId, $problemId] = explode('_', $key);

                $problem = $exam->problems->firstWhere('id', $problemId);
                $mark = (int) $mark;
                if ($mark < 0 || $mark > $problem->max_mark) {
                    $name1 = Student::find($studentId)->full_name;
                    $errors[] = "Student: {$name1}\n (max: {$problem->max_mark})";
                    continue;
                }
                Mark::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'problem_id' => $problemId,
                        'exam_id' => $data['exam_id'],
                    ],
                    [
                        'mark' => $mark,
                        'sinf_id' => $data['sinf_id'] ?? $record['sinf_id'] ?? null, // provide a valid value
                    ]
                );

            }
        }

        if (!empty($errors)) {
            Notification::make()
                ->title("Ba'zi baholarda xatolik")
                ->body(
                    ("Baholarni to'g'riliga va maximum balldan oshmasligiga ishonch hosil qiling! \n $errors[0]")
                )
                ->danger()
                ->send();

            throw ValidationException::withMessages($errors);
        }

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
