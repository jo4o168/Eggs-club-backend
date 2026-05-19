<?php

namespace App\Http\Resources\Stats;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProducerStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'productsCount' => $this['productsCount'] ?? 0,
            'pendingOrdersCount' => $this['pendingOrdersCount'] ?? 0,
            'totalRevenue' => (float) ($this['totalRevenue'] ?? 0),
            'subscribersCount' => $this['subscribersCount'] ?? 0,
        ];
    }
}
