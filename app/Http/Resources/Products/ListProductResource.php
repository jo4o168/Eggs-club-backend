<?php

namespace App\Http\Resources\Products;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**@var Product $this */

        return [
            'id' => $this->id,
            'name' => $this->name,
            'egg_size' => $this->egg_size,
            'egg_color' => $this->egg_color,
            'kit_quantity' => $this->kit_quantity,
            'description' => $this->description,
            'stock_quantity' => $this->stock_quantity,
            'unit' => $this->unit,
            'price' => $this->price,
            'subscription_price' => $this->subscription_price,
            'one_time_price' => $this->one_time_price,
            'allow_subscription' => $this->allow_subscription,
            'allow_one_time_purchase' => $this->allow_one_time_purchase,
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'producer_id' => $this->producer_id,
        ];
    }
}
