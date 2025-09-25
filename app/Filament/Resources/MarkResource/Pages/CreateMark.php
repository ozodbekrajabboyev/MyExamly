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

    // Remove or keep this method - it's not needed for your current logic
    // but won't hurt if you want to keep it for other fields
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // This method doesn't affect your marks array processing
        // but you can keep it for other field processing if needed
        return $data;
    }

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

            // Handle null/empty mark values - convert to 0 or skip
            if ($markValue === null || $markValue === '' || $markValue === false) {
                $markValue = 0; // Set default value
                // Alternative: continue; // Skip saving if you don't want to save empty marks
            }

            // Convert to numeric to ensure proper validation
            $markValue = (float) $markValue;

            // Validate mark value
            if ($markValue < 0 || $markValue > $problem['max_mark']) {
                continue; // Skip invalid marks
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
                // Log the error for debugging
                \Log::error("Error saving mark for student {$studentId}, problem {$problemId}: " . $e->getMessage());

                // Optionally show a notification about the specific error
                Notification::make()
                    ->title("Xatolik yuz berdi")
                    ->body("Talaba ID {$studentId} uchun baho saqlanmadi: " . $e->getMessage())
                    ->warning()
                    ->send();

                continue; // Continue with other marks
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
