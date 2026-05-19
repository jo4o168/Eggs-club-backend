<?php

namespace App\Http\Services\SubscriptionPlan;

use App\Http\Filters\Filter\DefaultFilter;
use App\Models\SubscriptionPlan;
use App\Models\User;

class ListSubscriptionPlanService
{
    public function run(DefaultFilter $filter, User $user)
    {
        $profileId = $user->profile->id;
        $role = is_array($user->roles) ? (int) ($user->roles[0] ?? 0) : (int) $user->roles;

        return SubscriptionPlan::query()
            ->when($role === 1, fn ($q) => $q->where('producer_id', $profileId))
            ->get();
    }
}
