<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function usageLogs()
    {
        return $this->hasMany(UsageLog::class);
    }

    /**
     * Get the sms credit associated with the user.
     */
    public function smsCredit()
    {
        return $this->hasOne(SmsCredit::class);
    }
}