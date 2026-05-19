<?php

namespace App\Http\Services\Order;

use App\Enum\OrderStatus;
use App\Models\Order;
use App\Models\User;

class UpdateOrderStatusService
{
    public function run(Order $order, string $status, User $user): void
    {
        $profileId = (int) $user->profile->id;
        $orderProducerId = (int) ($order->producer_id ?? 0);

        $isOwnerProducer = $orderProducerId === $profileId
            || $order->items()->whereHas('product', fn ($q) => $q->where('producer_id', $profileId))->exists()
            || $order->subscription()
                ->whereHas('subscriptionPlan', fn ($q) => $q->where('producer_id', $profileId))
                ->exists();

        abort_unless($isOwnerProducer, 403);

        $statusMap = [
            'pending' => OrderStatus::PENDING->value,
            'confirmed' => OrderStatus::CONFIRMED->value,
            'preparing' => OrderStatus::PREPARING->value,
            'shipped' => OrderStatus::SHIPPED->value,
            'delivered' => OrderStatus::DELIVERED->value,
            'cancelled' => OrderStatus::CANCELLED->value,
        ];

        $order->update(['status' => $statusMap[$status]]);
    }
}
