<?php

namespace App\Http\Services\Cart;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Collection;

class ListCartItemsService
{
    public function run(User $user): Collection
    {
        $customerId = (int) $user->profile->id;

        return CartItem::query()
            ->where('customer_id', $customerId)
            ->with(['product', 'subscriptionPlan'])
            ->orderBy('id')
            ->get();
    }
}
