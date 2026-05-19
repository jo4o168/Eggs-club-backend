<?php

namespace App\Models;

use App\Enum\CartPurchaseMode;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends BaseModel
{
    protected $fillable = [
        'customer_id',
        'product_id',
        'quantity',
        'purchase_mode',
        'subscription_plan_id',
    ];

    protected function casts(): array
    {
        return [
            'purchase_mode' => CartPurchaseMode::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'customer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
