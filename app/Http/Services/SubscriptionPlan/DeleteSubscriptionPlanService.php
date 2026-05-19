<?php

namespace App\Http\Services\SubscriptionPlan;

use App\Models\SubscriptionPlan;
use App\Models\User;

class DeleteSubscriptionPlanService
{
    public function run(SubscriptionPlan $subscriptionPlan, User $user): void
    {
        abort_unless((int) $subscriptionPlan->producer_id === (int) $user->profile->id, 403);
        $subscriptionPlan->delete();
    }
}
