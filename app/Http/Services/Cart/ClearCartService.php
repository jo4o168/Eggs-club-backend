<?php

namespace App\Http\Services\Cart;

use App\Models\CartItem;
use App\Models\User;

class ClearCartService
{
    public function run(User $user): void
    {
        $customerId = (int) $user->profile->id;
        CartItem::query()->where('customer_id', $customerId)->delete();
    }
}
