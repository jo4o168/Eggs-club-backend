<?php

namespace App\Http\Resources\Orders;

use App\Enum\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => strtolower(OrderStatus::tryFrom((int) $this->status)?->name ?? 'PENDING'),
            'total_amount' => (float) $this->total_amount,
            'producer_id' => (int) $this->producer_id,
            'customer' => $this->customer ? [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
            ] : null,
            'created_at' => $this->created_at,
        ];
    }
}
