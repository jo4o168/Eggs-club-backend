<?php

namespace App\Models;

class OrderItem extends BaseModel
{
    protected $fillable = [
        'product_name',
        'quantity',
        'unit_price',
        'order_id',
        'product_id',
    ];

}
