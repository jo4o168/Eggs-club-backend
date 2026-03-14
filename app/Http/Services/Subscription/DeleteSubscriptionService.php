<?php

namespace App\Http\Services\Subscription;

use App\Models\Subscription;

class DeleteSubscriptionService
{
    public function run(Subscription $subscription): void
    {
        $subscription->delete();
    }
}
