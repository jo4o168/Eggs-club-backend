<?php

namespace App\Http\Services\SubscriptionPlan;

use App\Models\SubscriptionPlan;

class DeleteSubscriptionPlanService
{
    public function run(SubscriptionPlan $subscriptionPlan): void
    {
        $subscriptionPlan->delete();
    }
}
