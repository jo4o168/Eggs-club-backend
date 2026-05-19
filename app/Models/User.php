<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property bool $master
 * @property UserPermission $userPermission
 * @property Profile $profile
 */
class User extends Authenticatable implements MustVerifyEmailContract
{
    use CanResetPassword, HasApiTokens, MustVerifyEmail, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'email_verified_at',
        'password',
        'roles',
        'active',
        'master',
        'remember_token',
        'active',
    ];

    protected $casts = [
        'roles' => 'json',
        'email_verified_at' => 'datetime',
    ];

    public function userPermissions(): HasOne
    {
        return $this->hasOne(UserPermission::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }
}
