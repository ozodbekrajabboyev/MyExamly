<?php

namespace App\Notifications;

use Filament\Notifications\Auth\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class FilamentResetPassword extends ResetPassword
{
    protected function getMailMessage($notifiable, string $url): MailMessage
    {
        return (new MailMessage)
            ->subject('Parolni tiklash')
            ->greeting('Assalomu alaykum!')
            ->line('Siz akkauntingiz uchun parolni tiklash so‘rovini yubordingiz.')
            ->action('Parolni tiklash', $url)
            ->line('Ushbu havola 60 daqiqa ichida amal qilish muddatini tugatadi.')
            ->line('Agar siz bu so‘rovni yubormagan bo‘lsangiz, hech qanday amal talab qilinmaydi.')
            ->salutation('Hurmat bilan, MyExamly');
    }
}
