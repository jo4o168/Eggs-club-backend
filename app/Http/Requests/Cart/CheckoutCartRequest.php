<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CheckoutCartRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $roles = $this->user()?->roles;
        $role = is_array($roles) ? (int) ($roles[0] ?? 0) : (int) $roles;

        return $role === 0;
    }

    public function rules(): array
    {
        return [
            'delivery_address' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'payment_method_id' => ['sometimes', 'nullable', 'integer', Rule::exists('payment_methods', 'id')->where('customer_id', $this->user()->profile->id)],
        ];
    }
}
