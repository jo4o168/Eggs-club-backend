<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $name
 */
class Profile extends BaseModel
{
    protected $fillable = [
        'name',
        'role',
        'email',
        'phone',
        'avatar_url',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
