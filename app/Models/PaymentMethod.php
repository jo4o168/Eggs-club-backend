<?php

namespace App\Models;

class PaymentMethod extends BaseModel
{
    protected $fillable = [
        'type',
        'card_last_four',
        'card_brand',
        'is_default',
        'customer_id',
    ];

}
