<?php

namespace App\Http\Services\Auth;

use App\Enum\ProfileRole;
use App\Models\ProducerSetting;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SignUpService
{
    public function run(array $data): void
    {
        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'roles' => $data['role'],
            ]);

            $profile = Profile::create([
                'name' => $data['name'],
                'role' => $data['role'],
                'email' => $data['email'],
                'user_id' => $user->id,
            ]);

            if ((int) $data['role'] === ProfileRole::PRODUCER->value) {
                ProducerSetting::create([
                    'farm_name' => $data['farm_name'] ?? $data['name'],
                    'address' => $data['location'] ?? null,
                    'producer_id' => $profile->id,
                ]);
            }

            return $user;
        });

        $user->sendEmailVerificationNotification();
    }

}
