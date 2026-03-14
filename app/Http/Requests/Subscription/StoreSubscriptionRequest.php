<?php

namespace App\Http\Requests\Subscription;

use App\Http\Requests\BaseRequest;

class StoreSubscriptionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'next_delivery_date' => ['sometimes', 'nullable', 'date'],
            'pause_until' => ['sometimes', 'nullable', 'date'],
            'subscription_plan_id' => ['required', 'integer', 'exists:subscription_plans,id', 'unique:subscriptions,subscription_plan_id'],
        ];
    }
}
