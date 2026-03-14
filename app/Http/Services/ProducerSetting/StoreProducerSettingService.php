<?php

namespace App\Http\Services\ProducerSetting;

use App\Models\ProducerSetting;

class StoreProducerSettingService
{
    public function run(array $data): void
    {
        ProducerSetting::create($data);
    }
}
