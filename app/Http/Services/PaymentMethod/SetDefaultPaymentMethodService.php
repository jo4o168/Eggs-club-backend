<?php

namespace App\Http\Services\PaymentMethod;

use App\Models\PaymentMethod;
use App\Models\User;

class SetDefaultPaymentMethodService
{
    public function run(PaymentMethod $paymentMethod, User $user): void
    {
        $profileId = $user->profile->id;
        abort_unless((int) $paymentMethod->customer_id === (int) $profileId, 403);

        PaymentMethod::query()->where('customer_id', $profileId)->update(['is_default' => false]);
        $paymentMethod->update(['is_default' => true]);
    }
}
