<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Plan;
use App\Models\SmsCredit;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // প্রথমে ইউজার তৈরি করা হচ্ছে
        $user = static::getModel()::create($data);

        // ডিফল্ট প্ল্যান খুঁজে বের করে সাবস্ক্রাইব করানো হচ্ছে
        $defaultPlan = Plan::where('is_default', true)->first() ?? Plan::where('name', 'Free')->first();
        if ($defaultPlan) {
            $user->subscription()->create([
                'plan_id' => $defaultPlan->id,
                'starts_at' => now(),
            ]);
        }
        
        // SMS ক্রেডিট এন্ট্রি তৈরি করা হচ্ছে
        SmsCredit::create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        return $user;
    }
}