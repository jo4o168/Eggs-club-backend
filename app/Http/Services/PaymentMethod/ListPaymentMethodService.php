<?php

namespace App\Http\Services\PaymentMethod;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Collection;

class ListPaymentMethodService
{
    public function run(User $user): Collection
    {
        $profileId = $user->profile->id;

        return PaymentMethod::query()
            ->where('customer_id', $profileId)
            ->latest()
            ->get();
    }
}
