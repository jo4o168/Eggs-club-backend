<?php

namespace App\Http\Services\Stats;

use App\Enum\SubscriptionStatus;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

class GetCustomerStatsService
{
    public function run(User $user): array
    {
        $profileId = $user->profile->id;

        $ordersCount = Order::query()->where('customer_id', $profileId)->count();
        $totalSpent = (float) Order::query()->where('customer_id', $profileId)->sum('total_amount');

        $subscription = Subscription::query()
            ->with(['subscriptionPlan', 'subscriptionPlan.producer'])
            ->where('customer_id', $profileId)
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->latest()
            ->first();

        $monthsSubscribed = 0;
        if ($subscription?->start_date) {
            $monthsSubscribed = Carbon::parse($subscription->start_date)->diffInMonths(Carbon::today());
        }

        return [
            'ordersCount' => $ordersCount,
            'monthsSubscribed' => $monthsSubscribed,
            'totalSpent' => $totalSpent,
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'next_delivery_date' => $subscription->next_delivery_date,
                'plan' => $subscription->subscriptionPlan ? [
                    'id' => $subscription->subscriptionPlan->id,
                    'name' => $subscription->subscriptionPlan->name,
                ] : null,
                'producer' => $subscription->subscriptionPlan?->producer ? [
                    'id' => $subscription->subscriptionPlan->producer->id,
                    'name' => $subscription->subscriptionPlan->producer->name,
                    'producer_settings' => [],
                ] : null,
            ] : null,
        ];
    }
}
