<?php

namespace App\Http\Services\ProducerSetting;

use App\Models\ProducerSetting;

class UpdateProducerSettingService
{
    public function run(array $data, ProducerSetting $producerSetting): void
    {
        $producerSetting->fill($data);
        $producerSetting->save();
    }
}
