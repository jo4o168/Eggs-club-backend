<?php

namespace App\Http\Services\Subscription;

use App\Http\Filters\Filter\DefaultFilter;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Collection;

class ListSubscriptionService
{
    public function run(DefaultFilter $filter, User $user): Collection
    {
        $profileId = $user->profile->id;
        $role = is_array($user->roles) ? (int) ($user->roles[0] ?? 0) : (int) $user->roles;

        $query = Subscription::query()->with(['subscriptionPlan', 'subscriptionPlan.producer.producerSetting']);
        if ($role === 1) {
            $query->whereHas('subscriptionPlan', fn ($q) => $q->where('producer_id', $profileId));
        } else {
            $query->where('customer_id', $profileId);
        }

        return $query->latest()->get();
    }
}
