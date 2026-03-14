<?php

namespace App\Http\Services\SubscriptionPlan;

use App\Models\SubscriptionPlan;

class ShowSubscriptionPlanService
{
    public function run(SubscriptionPlan $subscriptionPlan)
    {
        return $subscriptionPlan->where('id', $subscriptionPlan->id)->first();
    }
}
