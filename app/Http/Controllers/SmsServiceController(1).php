<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Throwable;

class SmsServiceController extends Controller
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    public function sendSmsPage()
    {
        return view('sms.send');
    }

    public function smsHistoryPage()
    {
        $smsLogs = Auth::user()->smsLogs()->latest()->paginate(20);
        return view('sms.history', compact('smsLogs'));
    }

    public function handleSendSms(Request $request)
    {
        $validated = $request->validate([
            'recipients' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $message = $validated['message'];
        $recipients = array_unique(array_filter(array_map('trim', explode(',', $validated['recipients']))));
        
        $smsParts = $this->creditService->calculateSmsCredits($message);
        $totalCreditsRequired = count($recipients) * $smsParts;

        if (!$this->creditService->hasSmsCredits($user, $totalCreditsRequired)) {
            return back()->withErrors(['message' => 'Insufficient SMS credits.']);
        }

        foreach ($recipients as $recipient) {
            $smsSentSuccessfully = $this->sendSmsViaGateway($recipient, $message);

            if ($smsSentSuccessfully) {
                $this->creditService->deductSmsCredits($user, $smsParts);
                SmsLog::create([
                    'user_id' => $user->id, 'recipient' => $recipient, 'message' => $message,
                    'source' => 'Manual', 'sms_parts' => $smsParts, 'credits_consumed' => $smsParts,
                ]);
            }
        }

        return redirect()->route('sms.send.page')->with('status', 'SMS messages have been sent successfully!');
    }

    // *** আসল SMS গেটওয়েতে কল করার জন্য নতুন ফাংশন ***
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

    private function formatPhoneNumberBd($number) {
        $cleaned = preg_replace('/[^0-9]/', '', $number);
        if (strlen($cleaned) == 11 && substr($cleaned, 0, 1) == '0') return '88' . $cleaned;
        if (strlen($cleaned) == 10 && substr($cleaned, 0, 1) == '1') return '880' . $cleaned;
        return $cleaned;
    }
}