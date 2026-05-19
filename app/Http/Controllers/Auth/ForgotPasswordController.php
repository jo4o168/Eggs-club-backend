<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpResponse;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            Password::broker()->sendResetLink($request->only('email'));
        } catch (TransportExceptionInterface $e) {
            report($e);

            $hint = 'Não foi possível enviar o e-mail. Confira MAIL_USERNAME, MAIL_PASSWORD (senha de app) e se a conta Google não bloqueou o acesso SMTP.';

            if (config('app.debug')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'hint' => $hint,
                ], 503);
            }

            return HttpResponse::error(null, $hint, 503);
        }

        return HttpResponse::ok(
            ['notice' => 'Se este e-mail estiver cadastrado, você receberá instruções para redefinir a senha.'],
            'success',
        );
    }
}
