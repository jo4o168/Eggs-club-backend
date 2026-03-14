<?php

namespace App\Models;

class Subscription extends BaseModel
{
    protected $fillable = [
        'status',
        'start_date',
        'next_delivery_date',
        'pause_until',
        'customer_id',
        'producer_id',
        'subscription_plan_id',
    ];

}
