<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends BaseModel
{
    protected $fillable = [
        'status',
        'start_date',
        'next_delivery_date',
        'pause_until',
        'customer_id',
        'subscription_plan_id',
        'payment_method_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'customer_id');
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

}
