<?php

namespace App\Http\Services\Subscription;

use App\Http\Filters\Filter\DefaultFilter;
use App\Models\Subscription;

class ListSubscriptionService
{
    public function run(DefaultFilter $filter)
    {
        $model = new Subscription();
        return $model->filterBy($filter)->get();
    }
}
