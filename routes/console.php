<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test {email}', function (string $email) {
    $this->line('Mailer padrão: '.config('mail.default'));
    $this->line('From: '.config('mail.from.address').' ('.config('mail.from.name').')');
    $pwdLen = strlen((string) config('mail.mailers.smtp.password'));
    $this->line('Tamanho da senha SMTP (já sem espaços): '.$pwdLen.' (Gmail app password = 16).');

    try {
        Mail::raw('Teste de envio SMTP (php artisan mail:test).', function ($message) use ($email) {
            $message->to($email)->subject('Eggs Club — teste SMTP');
        });
        $this->info('Envio concluído sem exceção. Confira inbox e spam.');
    } catch (\Throwable $e) {
        $this->error('Falha: '.$e->getMessage());
        $this->line($e->getFile().':'.$e->getLine());

        return 1;
    }

    return 0;
})->purpose('Envia um e-mail de teste e mostra erros SMTP na hora');
