<?php

namespace App\Http\Requests\Subscription;

use App\Enum\SubscriptionStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'integer', Rule::enum(SubscriptionStatus::class)],
            'next_delivery_date' => ['sometimes', 'nullable', 'date'],
            'pause_until' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
