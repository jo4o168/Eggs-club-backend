<?php

namespace App\Http\Services\SubscriptionPlan;

use App\Models\SubscriptionPlan;
use App\Models\User;

class UpdateSubscriptionPlanService
{
    public function run(array $data, SubscriptionPlan $subscriptionPlan, User $user): void
    {
        abort_unless((int) $subscriptionPlan->producer_id === (int) $user->profile->id, 403);
        $subscriptionPlan->fill($data);
        $subscriptionPlan->save();
    }
}
