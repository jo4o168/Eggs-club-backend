<?php

namespace App\Http\Services\SubscriptionPlan;

use App\Models\SubscriptionPlan;

class UpdateSubscriptionPlanService
{
    public function run(array $data, SubscriptionPlan $subscriptionPlan): void
    {
        $subscriptionPlan->fill($data);
        $subscriptionPlan->save();
    }
}
