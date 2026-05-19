<?php

namespace App\Http\Services\Product;

use App\Http\Filters\Filter\DefaultFilter;
use App\Models\Product;
use App\Models\User;

class ListProductService
{
    public function run(DefaultFilter $filter, User $user)
    {
        $profileId = $user->profile->id;
        $role = is_array($user->roles) ? (int) ($user->roles[0] ?? 0) : (int) $user->roles;

        return Product::query()
            ->when($role === 1, fn ($q) => $q->where('producer_id', $profileId))
            ->when($role === 0, fn ($q) => $q->where('is_active', true))
            ->get();
    }
}
