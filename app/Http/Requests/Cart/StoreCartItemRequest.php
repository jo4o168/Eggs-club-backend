<?php

namespace App\Http\Requests\Cart;

use App\Enum\CartPurchaseMode;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreCartItemRequest extends BaseRequest
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'purchase_mode' => ['required', 'string', Rule::enum(CartPurchaseMode::class)],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:999'],
            'subscription_plan_id' => ['sometimes', 'nullable', 'integer', 'exists:subscription_plans,id'],
        ];
    }
}
