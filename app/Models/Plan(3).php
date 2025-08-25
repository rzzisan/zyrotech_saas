<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'is_default', // <-- নতুন কলাম যোগ করা হয়েছে
        'daily_courier_limit',
        'monthly_courier_limit',
        'monthly_incomplete_order_limit',
    ];

    protected $casts = [
        'is_default' => 'boolean', // <-- কলামের টাইপ বলে দেওয়া হয়েছে
    ];
}