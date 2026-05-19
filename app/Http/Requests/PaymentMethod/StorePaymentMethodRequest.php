<?php

namespace App\Http\Requests\PaymentMethod;

use App\Http\Requests\BaseRequest;

class StorePaymentMethodRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:credit_card,debit_card,pix'],
            'last_four' => ['nullable', 'string', 'max:4'],
            'is_default' => ['sometimes', 'boolean'],
            'card_brand' => ['sometimes', 'nullable', 'string', 'max:255'],
            'gateway_id' => ['sometimes', 'string', 'max:255'],
            'expiration_month' => ['sometimes', 'integer'],
            'expiration_year' => ['sometimes', 'integer'],
        ];
    }
}
