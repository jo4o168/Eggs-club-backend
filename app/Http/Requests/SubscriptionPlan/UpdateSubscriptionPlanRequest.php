<?php

namespace App\Http\Requests\SubscriptionPlan;

use App\Enum\SubscriptionFrequency;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionPlanRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'numeric'],
            'eggs_quantity' => ['sometimes', 'integer'],
            'frequency' => ['sometimes', 'integer', Rule::enum(SubscriptionFrequency::class)],
        ];
    }
}
