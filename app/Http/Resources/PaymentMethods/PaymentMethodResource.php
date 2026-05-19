<?php

namespace App\Http\Resources\PaymentMethods;

use App\Enum\PaymentMethodType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => strtolower(PaymentMethodType::from((int) $this->type)->name),
            'last_four' => $this->card_last_four,
            'card_brand' => $this->card_brand,
            'expiration_month' => $this->expiration_month,
            'expiration_year' => $this->expiration_year,
            'is_default' => (bool) $this->is_default,
            'customer_id' => $this->customer_id,
        ];
    }
}
