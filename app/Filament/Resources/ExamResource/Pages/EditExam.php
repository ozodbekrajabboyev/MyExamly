<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Models\Sinf;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
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
        $teacher = Teacher::find($data['teacher_id'])->user;
        $class = Sinf::find($data['sinf_id'])->name ?? null;
        $subject = Subject::find($data['subject_id'])->name ?? null;
        $serial_number = $data['serial_number'] ?? null;
        $type = $data['type'];
        if($data['status'] === 'approved'){
            Notification::make()
                ->title('🎉 Imtihon tasdiqlandi!')
                ->body("$class-sinf | $subject | $serial_number-$type imtihoningiz tasdiqlandi va yuklab olish uchun tayyor.")
                ->icon('heroicon-o-check-badge')
                ->iconColor('success')
                ->duration(null) // Persistent notification
                ->sendToDatabase([$teacher]);
        }

        if($data['status'] === 'rejected'){
            Notification::make()
                ->title('Imtihon rad etildi')
                ->body("$class-sinf | $subject | $serial_number-$type imtihoningiz rad etildi. Iltimos, maʼlumotlarni qayta tekshirib, yana tasdiqlash uchun yuboring.")
                ->icon('heroicon-o-x-circle')
                ->iconColor('danger')
                ->duration(null) // Persistent notification
                ->sendToDatabase([$teacher]);
        }



        return $data;
    }
}
