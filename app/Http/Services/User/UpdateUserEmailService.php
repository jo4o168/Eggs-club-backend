<?php

namespace App\Http\Services\User;

use App\Models\User;

class UpdateUserEmailService
{
    public function run(User $user, string $email): void
    {
        $user->update(['email' => $email]);
    }
}
