<?php

namespace App\Http\Services\SubscriptionPlan;

use App\Models\SubscriptionPlan;

class StoreSubscriptionPlanService
{
    public function run(array $data): void
    {
        SubscriptionPlan::create($data);
    }
}
