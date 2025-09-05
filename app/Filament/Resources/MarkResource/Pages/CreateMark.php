<?php

namespace App\Filament\Resources\MarkResource\Pages;

use App\Filament\Resources\MarkResource;
use App\Models\Exam;
use App\Models\Mark;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\HtmlString;

class CreateMark extends CreateRecord
{
    protected static string $resource = MarkResource::class;

//    protected function getFormSchema(): array
//    {
//        return [
//            Section::make("O'quvchilarni baholarini kiriting")
//                ->description("O'quvchilarning baholarini kiritish uchun avval imtihonni tanlang")
//                ->icon('heroicon-o-information-circle')
//                ->schema([
//                    Forms\Components\Hidden::make('maktab_id')
//                        ->default(fn () => auth()->user()->maktab_id)
//                        ->required(),
//
//                    Forms\Components\Select::make('exam_id')
//                        ->label('Imtihon tanlang')
//                        ->options(function () {
//                            $user = auth()->user();
//
//                            $query = Exam::query()
//                                ->where('maktab_id', $user->maktab_id)
//                                ->whereNotNull('problems')
//                                ->with(['sinf', 'subject', 'teacher']);
//
//                            if ($user->role->name === 'teacher') {
//                                $query->where('teacher_id', $user->teacher->id);
//                            }
//
//                            return $query->get()
//                                ->filter(function ($exam) {
//                                    $problems = is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems;
//                                    return is_array($problems) && count($problems) > 0;
//                                })
//                                ->mapWithKeys(function ($exam) {
//                                    $label = "{$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}";
//                                    return [$exam->id => $label];
//                                });
//                        })
//                        ->reactive()
//                        ->required()
//                        ->afterStateUpdated(function (Get $get, $set, $state) {
//                            if (!$state) {
//                                $set('marks', []);
//                                return;
//                            }
//
//                            $exam = Exam::with(['sinf.students'])->find($state);
//                            if (!$exam || !$exam->problems) {
//                                $set('marks', []);
//                                return;
//                            }
//
//                            $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems);
//                            $marks = [];
//
//                            foreach ($exam->sinf->students as $student) {
//                                foreach ($problems as $problem) {
//                                    $existingMark = Mark::where('student_id', $student->id)
//                                        ->where('problem_id', $problem['id'])
//                                        ->where('exam_id', $exam->id)
//                                        ->first();
//
//                                    $marks["{$student->id}_{$problem['id']}"] = $existingMark ? $existingMark->mark : 0;
//                                }
//                            }
//
//                            $set('marks', $marks);
//                        }),
//
//                    Grid::make()
//                        ->schema(function (Get $get) {
//                            $examId = $get('exam_id');
//
//                            if (!$examId) {
//                                return [
//                                    Placeholder::make('')
//                                        ->content(new HtmlString("<div class='text-center py-8'><span class='text-gray-500 text-lg'>Iltimos, avval imtihonni tanlang</span></div>"))
//                                ];
//                            }
//
//                            $exam = Exam::with(['sinf.students'])->find($examId);
//                            if (!$exam || !$exam->problems) {
//                                return [
//                                    Placeholder::make('')
//                                        ->content(new HtmlString("<div class='text-center py-8'><span class='text-red-500 text-lg'>Tanlangan imtihonda topshiriqlar mavjud emas</span></div>"))
//                                ];
//                            }
//
//                            $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems);
//                            $students = $exam->sinf->students->sortBy('full_name');
//
//                            if ($problems->isEmpty()) {
//                                return [
//                                    Placeholder::make('')
//                                        ->content(new HtmlString("<div class='text-center py-8'><span class='text-red-500 text-lg'>Bu imtihonda hech qanday topshiriq yo'q</span></div>"))
//                                ];
//                            }
//
//                            if ($students->isEmpty()) {
//                                return [
//                                    Placeholder::make('')
//                                        ->content(new HtmlString("<div class='text-center py-8'><span class='text-orange-500 text-lg'>Bu sinfda hech qanday o'quvchi yo'q</span></div>"))
//                                ];
//                            }
//
//                            // Create header
//                            $header = [
//                                Placeholder::make('')
//                                    ->content(new HtmlString("<span class='font-bold text-gray-700'>O'quvchi / Topshiriq</span>"))
//                            ];
//
//                            foreach ($problems as $problem) {
//                                $header[] = Placeholder::make('')
//                                    ->content(new HtmlString("<span class='font-bold text-blue-600 text-center block'>Topshiriq {$problem['id']}<br><small class='text-gray-500'>(Max: {$problem['max_mark']})</small></span>"));
//                            }
//
//                            $schema = [Grid::make(count($header))->schema($header)];
//
//                            // Create student rows
//                            foreach ($students as $student) {
//                                $row = [
//                                    Placeholder::make('')
//                                        ->content(new HtmlString("<span class='font-medium text-gray-800'>{$student->full_name}</span>"))
//                                ];
//
//                                foreach ($problems as $problem) {
//                                    $row[] = Forms\Components\TextInput::make("marks.{$student->id}_{$problem['id']}")
//                                        ->hiddenLabel()
//                                        ->numeric()
//                                        ->minValue(0)
//                                        ->maxValue($problem['max_mark'])
//                                        ->step(0.1)
//                                        ->placeholder("0")
//                                        ->extraInputAttributes(['class' => 'text-center']);
//                                }
//
//                                $schema[] = Grid::make(count($row))->schema($row);
//                            }
//
//                            return $schema;
//                        })
//                        ->extraAttributes(['class' => 'mark-table border rounded-lg p-4 bg-gray-50'])
//                ])
//        ];
//    }

    protected function handleRecordCreation(array $data): Mark
    {
        $exam = Exam::findOrFail($data['exam_id']);

        $savedMarksCount = 0;
        $updatedMarksCount = 0;

        foreach ($data['marks'] as $key => $markValue) {
            [$studentId, $problemId] = explode('_', $key);

            // Validate that the problem exists in exam's JSON
            $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : $exam->problems);
            $problem = $problems->firstWhere('id', (int)$problemId);

            if (!$problem) {
                continue; // Skip invalid problems
            }

            // Validate mark value
            if ($markValue < 0 || $markValue > $problem['max_mark']) {
                continue; // Skip invalid marks
            }

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
        }

        // Send appropriate notification
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

        // Redirect to the marks index page
        $this->redirect(MarkResource::getUrl('index'));

        // Return a dummy Mark instance as required by CreateRecord
        return new Mark();
    }

    protected function getRedirectUrl(): string
    {
        return MarkResource::getUrl('index');
    }
}
