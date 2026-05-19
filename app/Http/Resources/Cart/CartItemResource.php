<?php

namespace App\Http\Resources\Cart;

use App\Enum\CartPurchaseMode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->product;
        $plan = $this->subscriptionPlan;
        $mode = $this->purchase_mode instanceof CartPurchaseMode
            ? $this->purchase_mode->value
            : (string) $this->purchase_mode;

        $unitPrice = $mode === CartPurchaseMode::ONE_TIME->value
            ? (float) ($product?->one_time_price ?? $product?->price ?? 0)
            : (float) ($plan?->price ?? $product?->subscription_price ?? $product?->price ?? 0);

        $qty = max(1, (int) $this->quantity);
        if ($mode === CartPurchaseMode::SUBSCRIPTION->value) {
            $qty = 1;
        }

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $qty,
            'purchase_mode' => $mode,
            'subscription_plan_id' => $this->subscription_plan_id,
            'unit_price' => $unitPrice,
            'line_total' => round($unitPrice * $qty, 2),
            'product' => $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'producer_id' => $product->producer_id,
                'allow_one_time_purchase' => (bool) $product->allow_one_time_purchase,
                'allow_subscription' => (bool) $product->allow_subscription,
                'image_url' => $product->image_url,
            ] : null,
            'plan' => $plan ? [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'producer_id' => $plan->producer_id,
            ] : null,
        ];
    }
}
