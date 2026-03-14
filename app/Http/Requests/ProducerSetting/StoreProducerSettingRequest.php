<?php

namespace App\Http\Requests\ProducerSetting;

use App\Http\Requests\BaseRequest;

class StoreProducerSettingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'farm_name' => ['required', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'address' => ['sometimes', 'nullable', 'string'],
            'city' => ['sometimes', 'nullable', 'string'],
            'state' => ['sometimes', 'nullable', 'string'],
            'delivery_info' => ['sometimes', 'nullable', 'string'],
            'accepts_new_subscribers' => ['sometimes', 'boolean'],
            'visible_in_search' => ['sometimes', 'boolean'],
            'producer_id' => ['required', 'integer', 'exists:profiles,id', 'unique:producer_settings,producer_id'],
        ];
    }
}
