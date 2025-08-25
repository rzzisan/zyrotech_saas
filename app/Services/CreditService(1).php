<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class CreditService
{
    /**
     * Checks if the user can use the courier check service for a specific phone number.
     * It only consumes credit on the first check of a unique phone number per day.
     */
    public function canUseCourierCheck(User $user, string $phoneNumber): bool
    {
        // আজকের দিনে এই ফোন নম্বরটি আগে চেক করা হয়েছে কিনা তা দেখা হচ্ছে
        $alreadyCheckedToday = $user->usageLogs()
            ->where('service_type', 'courier_check')
            ->where('details', $phoneNumber)
            ->whereDate('created_at', Carbon::today())
            ->exists();

        // যদি আজকের দিনে এই নম্বরটি আগে চেক করা হয়ে থাকে, তাহলে কোনো লিমিট পরীক্ষা ছাড়াই অনুমতি দেওয়া হবে
        if ($alreadyCheckedToday) {
            return true;
        }

        // যদি এটি আজকের দিনের জন্য একটি নতুন নম্বর হয়, তাহলে দৈনিক লিমিট পরীক্ষা করা হবে
        $subscription = $user->subscription;
        if (!$subscription) {
            return false;
        }

        $plan = $subscription->plan;

        // আজকের দিনে মোট কতগুলো ইউনিক ফোন নম্বর চেক করা হয়েছে তা গণনা করা হচ্ছে
        $dailyUniqueUsage = $user->usageLogs()
                                ->where('service_type', 'courier_check')
                                ->whereDate('created_at', Carbon::today())
                                ->distinct('details')
                                ->count('details');

        if ($dailyUniqueUsage >= $plan->daily_courier_limit) {
            return false; // দৈনিক লিমিট শেষ
        }
        
        // এখানে মাসিক লিমিটও একইভাবে ইউনিক নম্বরের উপর ভিত্তি করে পরীক্ষা করা যেতে পারে (আপাতত সরল রাখা হলো)

        return true;
    }

    /**
     * Records the usage for a courier check if it's a new phone number for the day.
     */
    public function recordCourierCheckUsage(User $user, string $phoneNumber): void
    {
        // আজকের দিনে এই ফোন নম্বরটি আগে চেক করা হয়েছে কিনা তা আবার দেখা হচ্ছে
        $alreadyCheckedToday = $user->usageLogs()
            ->where('service_type', 'courier_check')
            ->where('details', $phoneNumber)
            ->whereDate('created_at', Carbon::today())
            ->exists();
        
        // যদি আজকের দিনে এটি নতুন নম্বর হয়, শুধুমাত্র তখনই লগ তৈরি করা হবে
        if (!$alreadyCheckedToday) {
            $user->usageLogs()->create([
                'service_type' => 'courier_check',
                'details' => $phoneNumber,
            ]);
        }
    }
}