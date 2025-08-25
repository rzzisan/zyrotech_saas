<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the user's websites.
     */
    public function index()
    {
        $websites = Website::where('user_id', Auth::id())->latest()->get();
        return view('websites.index', ['websites' => $websites]);
    }

    /**
     * Store a newly created website in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain' => 'required|url|max:255',
        ]);

        $user = Auth::user();

        // একটি আসল এবং কার্যকরী Sanctum টোকেন তৈরি করা হচ্ছে
        $tokenName = 'zyro-connector-' . parse_url($validated['domain'], PHP_URL_HOST);
        $token = $user->createToken($tokenName);
        
        // ডাটাবেসে নতুন ওয়েবসাইট এবং আসল টোকেনটি সংরক্ষণ করা
        Website::create([
            'user_id' => $user->id,
            'domain' => $validated['domain'],
            'api_key' => $token->plainTextToken,
        ]);

        return redirect()->route('websites.index')->with('status', 'Website added successfully with a new secure API Key!');
    }

    /**
     * Remove the specified website from storage.
     */
    public function destroy(Website $website)
    {
        // নিশ্চিত করা হচ্ছে যে, শুধুমাত্র ওয়েবসাইটের মালিকই এটি ডিলিট করতে পারবে
        if ($website->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // ওয়েবসাইটের সাথে সম্পর্কিত সকল টোকেন মুছে ফেলা
        $user = Auth::user();
        $tokenName = 'zyro-connector-' . parse_url($website->domain, PHP_URL_HOST);
        $user->tokens()->where('name', $tokenName)->delete();

        // ডাটাবেস থেকে ওয়েবসাইটটি ডিলিট করা
        $website->delete();

        return redirect()->route('websites.index')->with('status', 'Website deleted successfully!');
    }
}