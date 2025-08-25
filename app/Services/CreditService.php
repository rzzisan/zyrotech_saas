<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class CreditService
{
    // --- Courier Check সম্পর্কিত ফাংশন (অপরিবর্তিত) ---
    public function canUseCourierCheck(User $user, string $phoneNumber): bool
    {
        $alreadyCheckedToday = $user->usageLogs()->where('service_type', 'courier_check')->where('details', $phoneNumber)->whereDate('created_at', Carbon::today())->exists();
        if ($alreadyCheckedToday) {
            return true;
        }
        $subscription = $user->subscription;
        if (!$subscription) { return false; }
        $plan = $subscription->plan;
        $dailyUniqueUsage = $user->usageLogs()->where('service_type', 'courier_check')->whereDate('created_at', Carbon::today())->distinct('details')->count('details');
        if ($dailyUniqueUsage >= $plan->daily_courier_limit) {
            return false;
        }
        return true;
    }

    public function recordCourierCheckUsage(User $user, string $phoneNumber): void
    {
        $alreadyCheckedToday = $user->usageLogs()->where('service_type', 'courier_check')->where('details', $phoneNumber)->whereDate('created_at', Carbon::today())->exists();
        if (!$alreadyCheckedToday) {
            $user->usageLogs()->create(['service_type' => 'courier_check', 'details' => $phoneNumber]);
        }
    }

    // --- SMS ক্রেডিট সম্পর্কিত নতুন ফাংশন ---

    /**
     * বার্তাটি ইউনিকোড (যেমন বাংলা) কিনা তা পরীক্ষা করে।
     */
    private function isUnicode(string $message): bool
    {
        return strlen($message) !== mb_strlen($message, 'UTF-8');
    }

    /**
     * বার্তার ধরনের উপর ভিত্তি করে কতগুলো এসএমএস ক্রেডিট প্রয়োজন হবে তা গণনা করে।
     */
    public function calculateSmsCredits(string $message): int
    {
        if ($this->isUnicode($message)) {
            // ইউনিকোড (বাংলা) বার্তার জন্য প্রতি ৭০ অক্ষরে ১টি ক্রেডিট
            $length = mb_strlen($message, 'UTF-8');
            return (int)ceil($length / 70);
        } else {
            // ইংরেজি (GSM) বার্তার জন্য প্রতি ১৬০ অক্ষরে ১টি ক্রেডিট
            $length = strlen($message);
            return (int)ceil($length / 160);
        }
    }

    /**
     * ব্যবহারকারীর অ্যাকাউন্টে প্রয়োজনীয় সংখ্যক এসএমএস ক্রেডিট আছে কিনা তা পরীক্ষা করে।
     */
    public function hasSmsCredits(User $user, int $requiredCredits = 1): bool
    {
        return $user->smsCredit && $user->smsCredit->balance >= $requiredCredits;
    }

    /**
     * ব্যবহারকারীর অ্যাকাউন্ট থেকে এসএমএস ক্রেডিট কেটে নেয়।
     */
    public function deductSmsCredits(User $user, int $creditsToDeduct = 1): bool
    {
        if (!$this->hasSmsCredits($user, $creditsToDeduct)) {
            return false;
        }
        
        $user->smsCredit->decrement('balance', $creditsToDeduct);
        return true;
    }
}