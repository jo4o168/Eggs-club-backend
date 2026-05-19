<?php

namespace App\Http\Services\SubscriptionPlan;

use App\Models\SubscriptionPlan;
use App\Models\User;

class StoreSubscriptionPlanService
{
    public function run(array $data, User $user): void
    {
        $data['producer_id'] = $user->profile->id;
        SubscriptionPlan::create($data);
    }
}
