<?php

namespace App\Http\Services\Subscription;

use App\Models\Subscription;

class UpdateSubscriptionService
{
    public function run(array $data, Subscription $subscription): void
    {
        $subscription->fill($data);
        $subscription->save();
    }
}
