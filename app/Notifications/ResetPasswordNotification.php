<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $name = trim((string) ($notifiable->name ?? '')) ?: 'tudo bem';

        return (new MailMessage)
            ->theme('eggsclub')
            ->subject('Redefinir senha do Egg\'s Club')
            ->markdown('mail.reset-password', [
                'name' => $name,
                'url' => $this->resetUrl($notifiable),
            ]);
    }
}
