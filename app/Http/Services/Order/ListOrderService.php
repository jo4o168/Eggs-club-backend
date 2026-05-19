<?php

namespace App\Http\Services\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;

class ListOrderService
{
    public function run(User $user): Collection
    {
        $profileId = $user->profile->id;
        $role = is_array($user->roles) ? (int) ($user->roles[0] ?? 0) : (int) $user->roles;

        $query = Order::query()->with(['customer:id,name', 'subscription.subscriptionPlan.producer:id,name']);

        if ($role === 1) {
            $query->where(function ($w) use ($profileId) {
                $w->where('producer_id', $profileId)
                    ->orWhereHas('items.product', fn ($q) => $q->where('producer_id', $profileId));
            });
        } else {
            $query->where('customer_id', $profileId);
        }

        return $query->latest()->get();
    }
}
