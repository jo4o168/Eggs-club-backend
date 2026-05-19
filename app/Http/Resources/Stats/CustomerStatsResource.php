<?php

namespace App\Http\Resources\Stats;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ordersCount' => $this['ordersCount'] ?? 0,
            'monthsSubscribed' => $this['monthsSubscribed'] ?? 0,
            'totalSpent' => (float) ($this['totalSpent'] ?? 0),
            'subscription' => $this['subscription'] ?? null,
        ];
    }
}
