<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Hidden attributes.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * One user has one subscription.
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * One user has many usage logs (if you use it).
     */
    public function usageLogs()
    {
        return $this->hasMany(UsageLog::class);
    }

    /**
     * One user has one sms credit (if you use it).
     */
    public function smsCredit()
    {
        return $this->hasOne(SmsCredit::class);
    }

    /**
     * Convenience relation: directly access user's Plan via Subscription.
     * This helps us show `plan.name` easily in Filament tables.
     */
    public function plan()
    {
        return $this->hasOneThrough(
            Plan::class,            // Final model
            Subscription::class,    // Intermediate model
            'user_id',              // Subscription FK to users
            'id',                   // Plan PK
            'id',                   // User PK
            'plan_id'               // Subscription FK to plans
        );
    }
}
