<?php

namespace App\Policies;

use App\Models\ProducerSetting;
use App\Models\User;

class ProducerSettingPolicy
{
    public function create(User $user): bool
    {
        if (! $user->profile) {
            return false;
        }

        $role = is_array($user->roles) ? (int) ($user->roles[0] ?? 0) : (int) $user->roles;

        return $role === 1;
    }

    public function view(User $user, ProducerSetting $producerSetting): bool
    {
        return $this->producerOwns($user, $producerSetting);
    }

    public function update(User $user, ProducerSetting $producerSetting): bool
    {
        return $this->producerOwns($user, $producerSetting);
    }

    private function producerOwns(User $user, ProducerSetting $producerSetting): bool
    {
        if (! $user->profile) {
            return false;
        }

        $role = is_array($user->roles) ? (int) ($user->roles[0] ?? 0) : (int) $user->roles;

        if ($role !== 1) {
            return false;
        }

        return (int) $producerSetting->producer_id === (int) $user->profile->id;
    }
}
