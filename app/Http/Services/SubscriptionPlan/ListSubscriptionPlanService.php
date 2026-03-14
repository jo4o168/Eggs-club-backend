<?php

namespace App\Http\Services\SubscriptionPlan;

use App\Http\Filters\Filter\DefaultFilter;
use App\Models\SubscriptionPlan;

class ListSubscriptionPlanService
{
    public function run(DefaultFilter $filter)
    {
        $model = new SubscriptionPlan();
        return $model->filterBy($filter)->get();
    }
}
