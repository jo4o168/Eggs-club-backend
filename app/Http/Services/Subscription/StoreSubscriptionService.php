<?php

namespace App\Http\Services\Subscription;

use App\Enum\SubscriptionFrequency;
use App\Enum\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StoreSubscriptionService
{
    public function run(array $data, ?User $user = null): Subscription
    {
        $user ??= Auth::user();
        $plan = SubscriptionPlan::query()->findOrFail((int) $data['subscription_plan_id']);

        abort_unless($plan->is_active, 422, 'Este plano não está disponível.');

        $payload = [
            'status' => SubscriptionStatus::ACTIVE->value,
            'start_date' => Carbon::today()->toDateString(),
            'next_delivery_date' => $this->nextDeliveryDate($plan->frequency)->toDateString(),
            'pause_until' => $data['pause_until'] ?? null,
            'customer_id' => $user->profile->id,
            'subscription_plan_id' => $plan->id,
            'payment_method_id' => $data['payment_method_id'] ?? null,
        ];

        return Subscription::create($payload);
    }

    private function nextDeliveryDate(int $frequency): Carbon
    {
        $today = Carbon::today();

        return match (SubscriptionFrequency::tryFrom((int) $frequency) ?? SubscriptionFrequency::MONTHLY) {
            SubscriptionFrequency::WEEKLY => $today->copy()->addWeek(),
            SubscriptionFrequency::BI_WEEKLY => $today->copy()->addWeeks(2),
            SubscriptionFrequency::MONTHLY => $today->copy()->addMonth(),
        };
    }
}
