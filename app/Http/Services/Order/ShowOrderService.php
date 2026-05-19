<?php

namespace App\Http\Services\Order;

use App\Models\Order;
use App\Models\User;

class ShowOrderService
{
    public function run(Order $order, User $user): Order
    {
        $profileId = (int) $user->profile->id;
        $role = is_array($user->roles) ? (int) ($user->roles[0] ?? 0) : (int) $user->roles;

        if ($role === 0) {
            abort_unless((int) $order->customer_id === $profileId, 403);
        } else {
            $orderProducerId = (int) ($order->producer_id ?? 0);
            $isOwnerProducer = $orderProducerId === $profileId
                || $order->items()->whereHas('product', fn ($q) => $q->where('producer_id', $profileId))->exists()
                || $order->subscription()
                    ->whereHas('subscriptionPlan', fn ($q) => $q->where('producer_id', $profileId))
                    ->exists();

            abort_unless($isOwnerProducer, 403);
        }

        return $order->load([
            'items',
            'customer:id,name,email',
            'subscription.subscriptionPlan',
        ]);
    }
}
