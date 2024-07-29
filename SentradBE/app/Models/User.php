<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, SoftDeletes, HasUuids, HasFactory, Notifiable;

    protected $keyType = 'uuid';
    protected $fillable = [
        'username',
        'email',
        'password',
        'email_verified_at',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];



    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->notify(new \App\Notifications\VerifyEmailNotification());
        });
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function roles()
    {
        return $this->hasManyThrough(Role::class, UserRole::class, 'user_id', 'id', 'id', 'role_id');
    }
    
    public function seniman()
    {
        return $this->hasOne(Seniman::class);
    }
}
