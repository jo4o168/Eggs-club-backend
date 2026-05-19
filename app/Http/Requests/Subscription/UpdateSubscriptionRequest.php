<?php

namespace App\Http\Requests\Subscription;

use App\Enum\SubscriptionStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $subscription = $this->route('subscription');
        if (! $subscription || ! $this->user()) {
            return false;
        }

        $profileId = (int) $this->user()->profile->id;
        $roles = $this->user()->roles;
        $role = is_array($roles) ? (int) ($roles[0] ?? 0) : (int) $roles;

        if ($role === 0) {
            return (int) $subscription->customer_id === $profileId;
        }

        if ($role === 1) {
            return (int) ($subscription->subscriptionPlan?->producer_id ?? 0) === $profileId;
        }

        return false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('plan_id') && !$this->has('subscription_plan_id')) {
            $this->merge(['subscription_plan_id' => $this->input('plan_id')]);
        }
    }

    public function rules(): array
    {
        $subscription = $this->route('subscription');

        return [
            'status' => ['sometimes', 'integer', Rule::enum(SubscriptionStatus::class)],
            'next_delivery_date' => ['sometimes', 'nullable', 'date'],
            'pause_until' => ['sometimes', 'nullable', 'date'],
            'subscription_plan_id' => [
                'sometimes',
                'integer',
                'exists:subscription_plans,id',
                Rule::unique('subscriptions', 'subscription_plan_id')
                    ->ignore($subscription?->id)
                    ->where(function ($query) use ($subscription) {
                        return $query->where('customer_id', $subscription?->customer_id)
                            ->whereIn('status', [
                                SubscriptionStatus::ACTIVE->value,
                                SubscriptionStatus::PAUSED->value,
                            ]);
                    }),
            ],
        ];
    }
}
