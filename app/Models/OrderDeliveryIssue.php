<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDeliveryIssue extends BaseModel
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'reason',
        'requested_resolution',
        'description',
        'evidence',
        'status',
        'resolution_message',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'evidence' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'customer_id');
    }
}
