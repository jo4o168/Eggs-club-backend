<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlan extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'eggs_quantity',
        'frequency',
        'is_active',
        'is_featured',
        'producer_id',
    ];

}
