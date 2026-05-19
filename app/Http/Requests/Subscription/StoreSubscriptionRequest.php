<?php

namespace App\Http\Requests\Subscription;

use App\Enum\SubscriptionStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $roles = $this->user()?->roles;
        $role = is_array($roles) ? (int) ($roles[0] ?? 0) : (int) $roles;

        return $role === 0;
    }

    public function rules(): array
    {
        return [
            'next_delivery_date' => ['sometimes', 'nullable', 'date'],
            'pause_until' => ['sometimes', 'nullable', 'date'],
            'subscription_plan_id' => [
                'required',
                'integer',
                'exists:subscription_plans,id',
                Rule::unique('subscriptions', 'subscription_plan_id')->where(function ($query) {
                    return $query->where('customer_id', $this->user()->profile->id)
                        ->whereIn('status', [
                            SubscriptionStatus::ACTIVE->value,
                            SubscriptionStatus::PAUSED->value,
                        ]);
                }),
            ],
            'payment_method_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('payment_methods', 'id')->where('customer_id', $this->user()->profile->id),
            ],
        ];
    }
}
