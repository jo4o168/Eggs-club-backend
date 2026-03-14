<?php

namespace App\Http\Requests\ProducerSetting;

use App\Http\Requests\BaseRequest;

class UpdateProducerSettingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'farm_name' => ['sometimes', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'address' => ['sometimes', 'nullable', 'string'],
            'city' => ['sometimes', 'nullable', 'string'],
            'state' => ['sometimes', 'nullable', 'string'],
            'delivery_info' => ['sometimes', 'nullable', 'string'],
            'accepts_new_subscribers' => ['sometimes', 'boolean'],
            'visible_in_search' => ['sometimes', 'boolean'],
        ];
    }
}
