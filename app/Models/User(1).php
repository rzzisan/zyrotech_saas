<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- রিলেশনশিপ ফাংশনগুলো এখন ক্লাসের ভিতরে সঠিক জায়গায় আছে ---

    /**
     * Get the subscription associated with the user.
     */
    public function subscription()
    {
        return $this->hasOne(\App\Models\Subscription::class);
    }

    /**
     * Get the usage logs for the user.
     */
    public function usageLogs()
    {
        return $this->hasMany(\App\Models\UsageLog::class);
    }
    
} // <-- এই closing brace-টি হলো ক্লাসের শেষ