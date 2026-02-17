<?php

namespace App\Filament\Resources\MarkResource\Pages;

use App\Filament\Resources\MarkResource;
use App\Models\Exam;
use App\Models\Mark;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditMark extends EditRecord
{
    protected static string $resource = MarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Get the first mark to determine the exam
        $mark = $this->record;
        $exam = $mark->exam;

        if (!$exam) {
            return $data;
        }

        $data['exam_id'] = $exam->id;
        $data['maktab_id'] = $exam->maktab_id;

        // Get all marks for this exam
        $allMarks = Mark::where('exam_id', $exam->id)->get();
        $marksArray = [];

        foreach ($allMarks as $markRecord) {
            $marksArray["{$markRecord->student_id}_{$markRecord->problem_id}"] = $markRecord->mark;
        }

        $data['marks'] = $marksArray;

        return $data;
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make("O'quvchilarni baholarini tahrirlash")
                ->description("O'quvchilarning baholarini tahrirlash")
                ->icon('heroicon-o-pencil')
                ->schema([
                    Forms\Components\Hidden::make('maktab_id')
                        ->default(fn () => auth()->user()->maktab_id)
                        ->required(),

                    Forms\Components\Select::make('exam_id')
                        ->label('Imtihon')
                        ->options(function () {
                            $mark = $this->record;
                            $exam = $mark->exam;

                            if (!$exam) return [];

                            return [
                                $exam->id => "{$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}"
                            ];
                        })
                        ->disabled()
                        ->dehydrated(),

                    Grid::make()
                        ->schema(function (Get $get) {
                            $mark = $this->record;
                            $exam = $mark->exam;

                            if (!$exam || !$exam->problems) {
                                return [
                                    Placeholder::make('')
                                        ->content(new HtmlString("<div class='text-center py-8'><span class='text-red-500 text-lg'>Imtihon topilmadi yoki topshiriqlar mavjud emas</span></div>"))
                                ];
                            }

                            $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems);
                            $students = $exam->sinf->students->sortBy('full_name');

                            if ($problems->isEmpty()) {
                                return [
                                    Placeholder::make('')
                                        ->content(new HtmlString("<div class='text-center py-8'><span class='text-red-500 text-lg'>Bu imtihonda hech qanday topshiriq yo'q</span></div>"))
                                ];
                            }

                            if ($students->isEmpty()) {
                                return [
                                    Placeholder::make('')
                                        ->content(new HtmlString("<div class='text-center py-8'><span class='text-orange-500 text-lg'>Bu sinfda hech qanday o'quvchi yo'q</span></div>"))
                                ];
                            }

                            // Create header
                            $header = [
                                Placeholder::make('')
                                    ->content(new HtmlString("<span class='font-bold text-gray-700'>O'quvchi / Topshiriq</span>"))
                            ];

                            foreach ($problems as $problem) {
                                $header[] = Placeholder::make('')
                                    ->content(new HtmlString("<span class='font-bold text-blue-600 text-center block'>Topshiriq {$problem['id']}<br><small class='text-gray-500'>(Max: {$problem['max_mark']})</small></span>"));
                            }

                            $schema = [Grid::make(count($header))->schema($header)];

                            // Create student rows
                            foreach ($students as $student) {
                                $row = [
                                    Placeholder::make('')
                                        ->content(new HtmlString("<span class='font-medium text-gray-800'>{$student->full_name}</span>"))
                                ];

                                foreach ($problems as $problem) {
                                    // Get existing mark for this student and problem
                                    $existingMark = Mark::where('student_id', $student->id)
                                        ->where('problem_id', $problem['id'])
                                        ->where('exam_id', $exam->id)
                                        ->first();

                                    $row[] = Forms\Components\TextInput::make("marks.{$student->id}_{$problem['id']}")
                                        ->hiddenLabel()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue($problem['max_mark'])
                                        ->step(0.1)
                                        ->default($existingMark ? $existingMark->mark : 0)
                                        ->extraInputAttributes(['class' => 'text-center']);
                                }

                                $schema[] = Grid::make(count($row))->schema($row);
                            }

                            return $schema;
                        })
                        ->extraAttributes(['class' => 'mark-table border rounded-lg p-4 bg-gray-50'])
                ])
        ];
    }

    protected function handleRecordUpdate(Mark|\Illuminate\Database\Eloquent\Model $record, array $data): Mark
    {
        $exam = Exam::findOrFail($record['exam_id']);

        $savedMarksCount = 0;
        $updatedMarksCount = 0;

        foreach ($data['marks'] as $key => $markValue) {
            [$studentId, $problemId] = explode('_', $key);

            $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems);
            $problem = $problems->firstWhere('id', (int) $problemId);

            if (!$problem) {
                continue;
            }

            // 🛡️ Null yoki bo‘sh qiymatni oldini olish
            if ($markValue === null || $markValue === '' || $markValue === false) {
                $markValue = 0;
            }

            $markValue = (float) $markValue;

            // ⚙️ Bahoni tekshirish
            if ($markValue < 0 || $markValue > $problem['max_mark']) {
                continue;
            }

            try {
                $mark = Mark::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'problem_id' => $problemId,
                        'exam_id' => $exam->id,
                    ],
                    [
                        'mark' => $markValue,
                        'sinf_id' => $exam->sinf_id,
                        'maktab_id' => $exam->maktab_id,
                    ]
                );

                if ($mark->wasRecentlyCreated) {
                    $savedMarksCount++;
                } else {
                    $updatedMarksCount++;
                }
            } catch (\Exception $e) {
                \Log::error("Error updating mark for student {$studentId}, problem {$problemId}: " . $e->getMessage());

                Notification::make()
                    ->title("Xatolik yuz berdi")
                    ->body("Talaba ID {$studentId} uchun baho yangilanmadi: " . $e->getMessage())
                    ->warning()
                    ->send();

                continue;
            }
        }

        // ✅ Xabar
        if ($savedMarksCount > 0 && $updatedMarksCount > 0) {
            $message = "{$savedMarksCount} ta yangi baho saqlandi va {$updatedMarksCount} ta baho yangilandi!";
        } elseif ($savedMarksCount > 0) {
            $message = "{$savedMarksCount} ta baho muvaffaqiyatli saqlandi!";
        } elseif ($updatedMarksCount > 0) {
            $message = "{$updatedMarksCount} ta baho muvaffaqiyatli yangilandi!";
        } else {
            $message = "Hech qanday baho o'zgartirilmadi.";
        }

        Notification::make()
            ->title($message)
            ->success()
            ->send();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return MarkResource::getUrl('index');
    }
}
