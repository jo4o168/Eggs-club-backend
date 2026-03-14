<?php

namespace App\Models;

class Order extends BaseModel
{
    protected $fillable = [
        'order_number',
        'delivery_address',
        'notes',
        'status',
        'total_amount',
        'customer_id',
        'producer_id',
        'subscription_plan_id',
    ];

}
