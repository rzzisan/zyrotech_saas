<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [ 'name', 'email', 'password', 'is_admin' ];
    protected $hidden = [ 'password', 'remember_token' ];
    protected $casts = [ 'email_verified_at' => 'datetime', 'password' => 'hashed' ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    public function subscription() { return $this->hasOne(Subscription::class); }
    public function usageLogs() { return $this->hasMany(UsageLog::class); }
    public function smsCredit() { return $this->hasOne(SmsCredit::class); }
}