<?php

namespace App\Http\Resources\Subscriptions;

use App\Enum\SubscriptionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => strtolower(SubscriptionStatus::tryFrom((int) $this->status)?->name ?? 'ACTIVE'),
            'plan_id' => $this->subscription_plan_id,
            'plan' => $this->subscriptionPlan,
            'producer_id' => $this->subscriptionPlan?->producer_id,
            'producer' => $this->subscriptionPlan?->producer ? [
                'id' => $this->subscriptionPlan->producer->id,
                'name' => $this->subscriptionPlan->producer->name,
                'producer_settings' => $this->subscriptionPlan->producer->producerSetting ? [
                    'farm_name' => $this->subscriptionPlan->producer->producerSetting->farm_name,
                    'city' => $this->subscriptionPlan->producer->producerSetting->city,
                    'state' => $this->subscriptionPlan->producer->producerSetting->state,
                ] : null,
            ] : null,
            'customer_id' => $this->customer_id,
            'start_date' => $this->start_date,
            'pause_until' => $this->pause_until,
            'next_delivery_date' => $this->next_delivery_date,
            'created_at' => $this->created_at,
        ];
    }
}
