<?php

namespace App\Http\Services\Auth;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SignInService
{
    public function run(array $data): array
    {
        if (!Auth::attempt($data)) {
            abort(401);
        }
        $user = User::query()->with('profile:id,user_id')->select('id', 'name', 'email', 'roles', 'email_verified_at')->find(Auth::id());

        if (! $user->hasVerifiedEmail()) {
            Auth::logout();

            abort(403, 'Confirme seu e-mail antes de entrar. Enviamos um link para o endereço usado no cadastro — verifique a caixa de entrada e o spam.');
        }
        if (!$user->profile) {
            $role = is_array($user->roles) ? (int) ($user->roles[0] ?? 0) : (int) $user->roles;
            $profile = Profile::create([
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
                'user_id' => $user->id,
            ]);
            $user->setRelation('profile', $profile);
        }
        if (is_array($user->roles)) {
            $user->roles = (int) ($user->roles[0] ?? 0);
        } else {
            $user->roles = (int) $user->roles;
        }
        $user->profile_id = $user->profile?->id;
        $accessToken = $user->createToken('auth_token')->plainTextToken;
        return compact('accessToken', 'user');


    }
}
