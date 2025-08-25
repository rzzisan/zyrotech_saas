<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller; // <-- মূল পরিবর্তনটি এখানে
use App\Models\User;
use App\Models\Plan;
use App\Models\SmsCredit;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // ডিফল্ট প্ল্যান খুঁজে বের করা হচ্ছে
            $defaultPlan = Plan::where('is_default', true)->first() ?? Plan::where('name', 'Free')->first();

            if ($defaultPlan) {
                $user->subscription()->create([
                    'plan_id' => $defaultPlan->id,
                    'starts_at' => now(),
                ]);
            }

            SmsCredit::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);

            event(new Registered($user));
            Auth::login($user);
        });

        return redirect(route('dashboard', absolute: false));
    }
}