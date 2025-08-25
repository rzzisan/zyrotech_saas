<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();

        // ব্যবহারকারীর সাবস্ক্রিপশন, প্ল্যান এবং এসএমএস ক্রেডিট তথ্য একসাথে লোড করা হচ্ছে
        $user->load('subscription.plan', 'smsCredit');

        $subscription = $user->subscription;
        $plan = $subscription? $subscription->plan : null;
        $smsCredit = $user->smsCredit;

        // আজকের দিনে কতগুলো ইউনিক ফোন নম্বর চেক করা হয়েছে তা গণনা করা হচ্ছে
        $dailyUsage = $user->usageLogs()
            ->where('service_type', 'courier_check')
            ->whereDate('created_at', now())
            ->distinct('details')
            ->count('details');

        // সকল তথ্য ভিউতে পাঠানো হচ্ছে
        return view('dashboard', [
            'plan' => $plan,
            'dailyUsage' => $dailyUsage,
            'smsCredit' => $smsCredit,
        ]);
    }
}