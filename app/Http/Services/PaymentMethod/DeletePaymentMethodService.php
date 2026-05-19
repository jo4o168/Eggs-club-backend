<?php

namespace App\Http\Services\PaymentMethod;

use App\Models\PaymentMethod;
use App\Models\User;

class DeletePaymentMethodService
{
    public function run(PaymentMethod $paymentMethod, User $user): void
    {
        abort_unless((int) $paymentMethod->customer_id === (int) $user->profile->id, 403);
        $paymentMethod->delete();
    }
}
