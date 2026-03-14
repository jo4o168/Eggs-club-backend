<?php

namespace App\Http\Services\ProducerSetting;

use App\Models\ProducerSetting;

class ShowProducerSettingService
{
    public function run(ProducerSetting $producerSetting)
    {
        return $producerSetting->where('id', $producerSetting->id)->first();
    }
}
