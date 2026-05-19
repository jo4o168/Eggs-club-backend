<?php

namespace App\Http\Services\ProducerSetting;

use App\Models\ProducerSetting;
use App\Models\User;

class ShowMyProducerSettingService
{
    public function run(User $user): ?ProducerSetting
    {
        return ProducerSetting::query()
            ->where('producer_id', $user->profile->id)
            ->first();
    }
}
