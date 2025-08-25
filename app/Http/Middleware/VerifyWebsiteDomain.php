<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class VerifyWebsiteDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // রিকোয়েস্ট থেকে User-Agent হেডারটি নেওয়া
        $userAgent = $request->header('User-Agent');
        
        // User-Agent থেকে ওয়ার্ডপ্রেস সাইটের URL (https://zyrotechbd.com) বের করা
        preg_match('/WordPress\/\S+; (https?:\/\/\S+)/', $userAgent, $matches);
        $requestUrl = $matches[1] ?? null;

        if (!$requestUrl) {
            return response()->json(['error' => 'Invalid request origin.'], 403);
        }

        // URL থেকে শুধুমাত্র host বা ডোমেইন নামটি (zyrotechbd.com) বের করা
        $requestHost = parse_url($requestUrl, PHP_URL_HOST);

        if (!$requestHost) {
            return response()->json(['error' => 'Could not parse domain from origin.'], 403);
        }

        // বর্তমানে ব্যবহৃত API টোকেনের তথ্য বের করা
        $currentToken = Auth::user()->currentAccessToken();

        // টোকেনের নাম থেকে ডোমেইন নামটি বের করা (e.g., "zyro-connector-zyrotechbd.com" -> "zyrotechbd.com")
        $tokenHost = str_replace('zyro-connector-', '', $currentToken->name);

        // রিকোয়েস্টের ডোমেইন এবং টোকেনের ডোমেইন এক কিনা তা কঠোরভাবে পরীক্ষা করা
        if ($requestHost !== $tokenHost) {
            return response()->json(['error' => 'This API key is not authorized for this website.'], 403);
        }

        return $next($request);
    }
}