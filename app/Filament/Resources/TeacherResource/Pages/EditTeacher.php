<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeacher extends EditRecord
{
    protected static string $resource = TeacherResource::class;


    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Check if email is being changed and validate uniqueness
        if (isset($data['email'])) {
            $currentUserId = $this->record->user_id;

            if (User::where('email', $data['email'])
                ->where('id', '!=', $currentUserId)
                ->exists()) {
                $this->addError('data.email', 'Bu elektron pochta manzili allaqachon mavjud.');
                $this->halt();
            }

            // Update the email in users table
            User::where('id', $currentUserId)
                ->update(['email' => $data['email']]);

            // Remove email from data to prevent updating teachers table
            unset($data['email']);
        }

        return $data;
    }

}
