<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Users extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    protected $table = 'users';
    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'image',
        'address',
        'phone_number',
        'birthdate',
        'gender',
        'bio',
        'role',
        'type',
        'verification_code',
        'forgot_password_code',
        'is_verified',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function foods()
    {
        return $this->hasMany(Foods::class, 'user_id');
    }
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
