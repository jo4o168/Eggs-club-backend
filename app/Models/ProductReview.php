<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends BaseModel
{
    protected $fillable = [
        'rating',
        'comment',
        'customer_id',
        'product_id',
        'order_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'customer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
