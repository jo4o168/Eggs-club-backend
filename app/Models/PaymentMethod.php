<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends BaseModel
{
    protected $fillable = [
        'type',
        'card_last_four',
        'card_brand',
        'gateway_id',
        'expiration_month',
        'expiration_year',
        'is_default',
        'customer_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'customer_id');
    }
}
