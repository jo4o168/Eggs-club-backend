<?php

namespace App\Http\Services\Subscription;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;

class UpdateSubscriptionService
{
    public function run(array $data, Subscription $subscription): void
    {
        if (isset($data['subscription_plan_id'])) {
            $plan = SubscriptionPlan::query()->findOrFail((int) $data['subscription_plan_id']);
            abort_unless($plan->is_active, 422, 'Este plano não está disponível.');

            $currentProducerId = (int) ($subscription->subscriptionPlan?->producer_id ?? 0);
            abort_unless(
                $currentProducerId === 0 || (int) $plan->producer_id === $currentProducerId,
                422,
                'O novo plano precisa ser do mesmo produtor da assinatura atual.',
            );
        }

        $subscription->fill($data);
        $subscription->save();
    }
}
