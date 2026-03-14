<?php

namespace App\Http\Services\SubscriptionPlan;

use App\Models\SubscriptionPlan;

class ToggleFeaturedSubscriptionPlanService
{
    public function run(SubscriptionPlan $subscriptionPlan): void
    {
        $subscriptionPlan->is_featured = $subscriptionPlan->is_featured == 1 ? 0 : 1;
        $subscriptionPlan->save();
    }
}
