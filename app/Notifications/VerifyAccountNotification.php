<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyAccountNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $name = trim((string) ($notifiable->name ?? '')) ?: 'tudo bem';

        return (new MailMessage)
            ->theme('eggsclub')
            ->subject('Confirme seu e-mail no Egg\'s Club')
            ->markdown('mail.verify-account', [
                'name' => $name,
                'url' => $this->verificationUrl($notifiable),
            ]);
    }
}
