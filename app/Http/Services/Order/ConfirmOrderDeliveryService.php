<?php

namespace App\Http\Services\Order;

use App\Enum\OrderStatus;
use App\Models\Order;
use App\Models\User;

class ConfirmOrderDeliveryService
{
    public function run(Order $order, User $user): Order
    {
        $user->loadMissing('profile');
        abort_unless($user->isClient(), 403);
        abort_unless((int) $order->customer_id === (int) $user->profile->id, 403);
        abort_unless(
            (int) $order->status === OrderStatus::SHIPPED->value,
            422,
            'Confirme o recebimento só depois que o pedido estiver a caminho.'
        );

        $order->update(['status' => OrderStatus::DELIVERED->value]);

        return $order->fresh();
    }
}
