<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Link inválido ou expirado.');
        }

        $id = (int) $request->route('id');
        $hash = (string) $request->route('hash');

        $user = User::query()->findOrFail($id);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Link inválido.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
            event(new Verified($user));
        }

        $frontend = rtrim((string) Config::get('app.frontend_url'), '/');

        return redirect()->away($frontend.'/login?verified=1');
    }
}
