<?php

namespace App\Http\Services\Review;

use App\Enum\OrderStatus;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class StoreProductReviewService
{
    public function run(Order $order, array $data, User $user): ProductReview
    {
        $user->loadMissing('profile');
        abort_unless($user->isClient(), 403);
        abort_unless((int) $order->customer_id === (int) $user->profile->id, 403);
        abort_if((int) $order->status === OrderStatus::CANCELLED->value, 422, 'Pedido cancelado não pode ser avaliado.');
        abort_unless(
            (int) $order->status === OrderStatus::DELIVERED->value,
            422,
            'Só é possível avaliar depois que o pedido for entregue.'
        );

        $productId = (int) $data['product_id'];
        $bought = $order->items()->where('product_id', $productId)->exists();
        if (! $bought) {
            throw ValidationException::withMessages([
                'product_id' => ['Este kit não faz parte do pedido.'],
            ]);
        }

        $existing = ProductReview::query()
            ->where('customer_id', $user->profile->id)
            ->where('order_id', $order->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->fill([
                'rating' => (int) $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);
            $existing->save();

            return $existing;
        }

        return ProductReview::query()->create([
            'rating' => (int) $data['rating'],
            'comment' => $data['comment'] ?? null,
            'customer_id' => $user->profile->id,
            'product_id' => $productId,
            'order_id' => $order->id,
        ]);
    }
}
