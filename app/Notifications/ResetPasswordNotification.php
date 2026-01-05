<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;

    public $locale;

    public function __construct($token)
    {
        parent::__construct($token);
        $this->locale = app()->getLocale();
    }

    public function toMail($notifiable)
    {
        // Set the locale explicitly for this notification
        $currentLocale = app()->getLocale();
        app()->setLocale($this->locale);

        $mailMessage = (new MailMessage)
            ->subject(__('notifications.reset_password.subject'))
            ->greeting(__('notifications.reset_password.greeting'))
            ->line(__('notifications.reset_password.line1'))
            ->action(
                __('notifications.reset_password.action'),
                url(route('filament.app.auth.password-reset.reset', [
                        'token' => $this->token,
                        'email' => $notifiable->email,
                    ]))
            )
            ->line(__('notifications.reset_password.line2', [
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')
            ]))
            ->line(__('notifications.reset_password.line3'))
            ->salutation(__('notifications.reset_password.salutation'));

        // Restore the original locale
        app()->setLocale($currentLocale);

        return $mailMessage;
    }
}
