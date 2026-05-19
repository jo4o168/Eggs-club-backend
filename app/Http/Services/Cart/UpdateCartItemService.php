<?php

namespace App\Http\Services\Cart;

use App\Enum\CartPurchaseMode;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UpdateCartItemService
{
    public function run(CartItem $item, array $data, User $user): CartItem
    {
        abort_unless((int) $item->customer_id === (int) $user->profile->id, 403);

        $product = Product::query()->findOrFail((int) $item->product_id);
        $mode = isset($data['purchase_mode'])
            ? CartPurchaseMode::from($data['purchase_mode'])
            : ($item->purchase_mode instanceof CartPurchaseMode ? $item->purchase_mode : CartPurchaseMode::from((string) $item->purchase_mode));

        abort_unless($product->is_active, 422, 'Este kit de ovos não está disponível.');

        if ($mode === CartPurchaseMode::ONE_TIME) {
            abort_unless((bool) $product->allow_one_time_purchase, 422, 'Este kit não aceita compra única.');
            $planId = null;
            $quantity = isset($data['quantity']) ? max(1, (int) $data['quantity']) : max(1, (int) $item->quantity);
        } else {
            abort_unless((bool) $product->allow_subscription, 422, 'Este kit não aceita assinatura.');
            $planId = isset($data['subscription_plan_id'])
                ? (int) $data['subscription_plan_id']
                : (int) ($item->subscription_plan_id ?? 0);
            if ($planId < 1) {
                throw ValidationException::withMessages(['subscription_plan_id' => ['Selecione um plano de assinatura.']]);
            }
            $plan = SubscriptionPlan::query()->findOrFail($planId);
            abort_unless($plan->is_active, 422, 'Este plano não está disponível.');
            abort_unless((int) $plan->producer_id === (int) $product->producer_id, 422, 'O plano não pertence a este produtor.');
            $quantity = 1;
        }

        $item->fill([
            'purchase_mode' => $mode->value,
            'quantity' => $quantity,
            'subscription_plan_id' => $planId,
        ]);
        $item->save();

        return $item->fresh(['product', 'subscriptionPlan']);
    }
}
