<?php

namespace App\Http\Requests\SubscriptionPlan;

use App\Enum\SubscriptionFrequency;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreSubscriptionPlanRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['required', 'numeric'],
            'eggs_quantity' => ['required', 'integer'],
            'frequency' => ['sometimes', 'integer', Rule::enum(SubscriptionFrequency::class)],
            'producer_id' => ['sometimes', 'integer', 'exists:profiles,id'],
        ];
    }
}
