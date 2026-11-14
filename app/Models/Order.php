<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends BaseModel
{
    protected $fillable = [
        'order_number',
        'delivery_address',
        'notes',
        'producer_message',
        'nfe_access_key',
        'nfe_number',
        'nfe_issued_at',
        'status',
        'total_amount',
        'customer_id',
        'producer_id',
        'subscription_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'customer_id');
    }

    public function producer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'producer_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function deliveryIssue(): HasOne
    {
        return $this->hasOne(OrderDeliveryIssue::class);
    }

    protected function casts(): array
    {
        return [
            'nfe_issued_at' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    public function scopeForProducer(Builder $query, int $profileId): Builder
    {
        return $query->where(function (Builder $owner) use ($profileId) {
            $owner->where('producer_id', $profileId)
                ->orWhereHas('items.product', fn (Builder $q) => $q->where('producer_id', $profileId))
                ->orWhereHas('subscription.subscriptionPlan', fn (Builder $q) => $q->where('producer_id', $profileId));
        });
    }
}
