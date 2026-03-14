<?php

namespace App\Http\Services\Subscription;

use App\Models\Subscription;

class ShowSubscriptionService
{
    public function run(Subscription $subscription)
    {
        return $subscription->where('id', $subscription->id)->first();
    }
}
