<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use App\Services\CreditService;
use App\Traits\PhoneNumberFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Throwable;

class SmsController extends Controller
{
    use PhoneNumberFormatter;

    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    public function sendOrderStatusSms(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
            'source' => 'nullable|string|max:255', // <-- মূল পরিবর্তন: source এখন বাধ্যতামূলক নয়
        ]);

        $user = Auth::user();
        $message = $validated['message'];
        $requiredCredits = $this->creditService->calculateSmsCredits($message);

        if (!$this->creditService->hasSmsCredits($user, $requiredCredits)) {
            return response()->json(['error' => 'Insufficient SMS credits.'], 402);
        }

        $smsSentSuccessfully = $this->sendSmsViaGateway($validated['phone'], $message);

        if ($smsSentSuccessfully) {
            $this->creditService->deductSmsCredits($user, $requiredCredits);
            SmsLog::create([
                'user_id' => $user->id,
                'recipient' => $validated['phone'],
                'message' => $message,
                'source' => $validated['source'] ?? 'Plugin Quick SMS', // একটি ডিফল্ট সোর্স যোগ করা হয়েছে
                'sms_parts' => $requiredCredits,
                'credits_consumed' => $requiredCredits,
            ]);
            return response()->json([
                'message' => 'SMS sent successfully.',
                'credits_consumed' => $requiredCredits,
                'credits_remaining' => $user->fresh()->smsCredit->balance,
            ]);
        }
        
        return response()->json(['error' => 'Failed to send SMS via gateway.'], 502);
    }
    
    public function getCredits()
    {
        $user = Auth::user();
        $balance = $user->smsCredit->balance ?? 0;
        return response()->json(['success' => true, 'credits' => $balance]);
    }

    private function sendSmsViaGateway(string $recipient, string $message): bool
    {
        try {
            $phone = $this->formatPhoneNumberBd($recipient);
            $response = Http::get(config('services.sms_gateway.url'), [
                'apikey' => config('services.sms_gateway.api_key'),
                'secretkey' => config('services.sms_gateway.secret_key'),
                'callerID' => config('services.sms_gateway.sender_id'),
                'toUser' => $phone,
                'messageContent' => $message,
            ]);
            return $response->successful();
        } catch (Throwable $e) {
            return false;
        }
    }
}