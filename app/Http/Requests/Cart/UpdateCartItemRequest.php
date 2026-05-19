<?php

namespace App\Http\Requests\Cart;

use App\Enum\CartPurchaseMode;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateCartItemRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $roles = $this->user()?->roles;
        $role = is_array($roles) ? (int) ($roles[0] ?? 0) : (int) $roles;
        if ($role !== 0) {
            return false;
        }

        $item = $this->route('cartItem');

        return $item && (int) $item->customer_id === (int) $this->user()->profile->id;
    }

    public function rules(): array
    {
        return [
            'purchase_mode' => ['sometimes', 'string', Rule::enum(CartPurchaseMode::class)],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:999'],
            'subscription_plan_id' => ['sometimes', 'nullable', 'integer', 'exists:subscription_plans,id'],
        ];
    }
}
