<?php

namespace App\Http\Services\SubscriptionPlan;

use App\Models\SubscriptionPlan;

class ToggleActiveSubscriptionPlanService
{
    public function run(SubscriptionPlan $subscriptionPlan): void
    {
        $subscriptionPlan->is_active = $subscriptionPlan->is_active == 1 ? 0 : 1;
        $subscriptionPlan->save();
    }
}
