<?php

namespace App\Http\Services\ProducerSetting;

use App\Models\ProducerSetting;
use Illuminate\Support\Facades\Schema;

class UpdateProducerSettingService
{
    public function run(array $data, ProducerSetting $producerSetting): void
    {
        $data = $this->filterExistingColumns($data);

        $producerSetting->fill($data);
        $producerSetting->save();
    }

    private function filterExistingColumns(array $data): array
    {
        $validColumns = array_flip(Schema::getColumnListing('producer_settings'));

        return array_intersect_key($data, $validColumns);
    }
}
