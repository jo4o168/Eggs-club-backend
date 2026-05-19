<?php

namespace App\Http\Services\ProducerSetting;

use App\Models\ProducerSetting;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class UpsertMyProducerSettingService
{
    public function run(array $data, User $user): void
    {
        $producerId = $user->profile->id;

        $data = $this->filterExistingColumns($data);

        $setting = ProducerSetting::query()->firstOrNew(['producer_id' => $producerId]);
        $setting->fill($data);
        $setting->producer_id = $producerId;
        $setting->save();
    }

    private function filterExistingColumns(array $data): array
    {
        $validColumns = array_flip(Schema::getColumnListing('producer_settings'));

        return array_intersect_key($data, $validColumns);
    }
}
