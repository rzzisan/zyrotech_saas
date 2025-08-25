<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Filament\Resources\PlanResource;
use App\Models\Plan;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePlan extends CreateRecord
{
    protected static string $resource = PlanResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // যদি নতুন প্ল্যানটিকে ডিফল্ট হিসেবে সেট করা হয়
        if ($data['is_default']) {
            // তাহলে অন্য সকল প্ল্যান থেকে ডিফল্ট স্ট্যাটাস মুছে ফেলা হচ্ছে
            DB::transaction(function () {
                Plan::where('is_default', true)->update(['is_default' => false]);
            });
        }

        return static::getModel()::create($data);
    }
}