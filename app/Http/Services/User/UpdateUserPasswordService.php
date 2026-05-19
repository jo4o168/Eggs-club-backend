<?php

namespace App\Http\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUserPasswordService
{
    public function run(User $user, string $currentPassword, string $newPassword): void
    {
        abort_unless(Hash::check($currentPassword, $user->password), 422, 'Senha atual incorreta');
        $user->update(['password' => Hash::make($newPassword)]);
    }
}
