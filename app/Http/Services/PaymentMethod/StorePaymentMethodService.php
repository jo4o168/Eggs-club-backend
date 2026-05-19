<?php

namespace App\Http\Services\PaymentMethod;

use App\Enum\PaymentMethodType;
use App\Models\PaymentMethod;
use App\Models\User;

class StorePaymentMethodService
{
    public function run(array $data, User $user): void
    {
        $typeMap = [
            'credit_card' => PaymentMethodType::CREDIT_CARD->value,
            'debit_card' => PaymentMethodType::DEBIT_CARD->value,
            'pix' => PaymentMethodType::PIX->value,
        ];

        $profileId = $user->profile->id;

        if (($data['is_default'] ?? false) === true) {
            PaymentMethod::query()->where('customer_id', $profileId)->update(['is_default' => false]);
        }

        PaymentMethod::create([
            'type' => $typeMap[$data['type']],
            'card_last_four' => $data['last_four'] ?? null,
            'card_brand' => $data['card_brand'] ?? null,
            'is_default' => (bool) ($data['is_default'] ?? false),
            'gateway_id' => $data['gateway_id'] ?? 'manual',
            'expiration_month' => (int) ($data['expiration_month'] ?? 1),
            'expiration_year' => (int) ($data['expiration_year'] ?? 2099),
            'customer_id' => $profileId,
        ]);
    }
}
