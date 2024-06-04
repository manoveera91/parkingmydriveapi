<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AuthUser extends Authenticatable
{
    use HasApiTokens,HasFactory,Notifiable;
    // use HasApiTokens,HasFactory,Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'mobile',
        'socialID'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getAuthPasswordBroker()
    {
        return \Password::broker('auth_users');
    }
}
