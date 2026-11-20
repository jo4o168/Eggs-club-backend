<?php

namespace App\Http\Services\Order;

use App\Enum\OrderStatus;
use App\Models\Order;
use App\Models\User;

class UpdateOrderStatusService
{
    public function run(Order $order, array $data, User $user): void
    {
        $user->loadMissing('profile');
        $profileId = (int) $user->profile->id;

        abort_unless(
            Order::query()->whereKey($order->id)->forProducer($profileId)->exists(),
            403
        );

        $statusKey = (string) $data['status'];
        $statusMap = [
            'pending' => OrderStatus::PENDING->value,
            'confirmed' => OrderStatus::CONFIRMED->value,
            'preparing' => OrderStatus::PREPARING->value,
            'shipped' => OrderStatus::SHIPPED->value,
            'delivered' => OrderStatus::DELIVERED->value,
            'cancelled' => OrderStatus::CANCELLED->value,
        ];

        $status = OrderStatus::from($statusMap[$statusKey]);
        $payload = [
            'status' => $status->value,
        ];

        if ($status === OrderStatus::CANCELLED) {
            $payload['producer_message'] = trim((string) $data['producer_message']);
        } else {
            $custom = trim((string) ($data['producer_message'] ?? ''));
            $payload['producer_message'] = $custom !== ''
                ? $custom
                : $status->customerNotice();
        }

        $order->update($payload);
    }
}
