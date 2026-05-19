<?php

namespace App\Http\Services\Cart;

use App\Models\CartItem;
use App\Models\User;

class DeleteCartItemService
{
    public function run(CartItem $item, User $user): void
    {
        abort_unless((int) $item->customer_id === (int) $user->profile->id, 403);
        $item->delete();
    }
}
