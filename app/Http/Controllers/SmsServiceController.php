<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Services\CreditService;
use App\Traits\PhoneNumberFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Throwable;

class SmsServiceController extends Controller
{
    use PhoneNumberFormatter;

    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    /**
     * Display the Send SMS page.
     */
    public function sendSmsPage()
    {
        return view('sms.send');
    }

    /**
     * Display the SMS History page.
     */
    public function smsHistoryPage()
    {
        $smsLogs = Auth::user()->smsLogs()->latest()->paginate(20);
        return view('sms.history', compact('smsLogs'));
    }

    /**
     * Handle the form submission for sending SMS.
     */
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
            return back()->withErrors(['message' => 'Insufficient SMS credits to send messages to all recipients.']);
        }

        foreach ($recipients as $recipient) {
            if (empty($recipient)) continue;

            $smsSentSuccessfully = $this->sendSmsViaGateway($recipient, $message);

            if ($smsSentSuccessfully) {
                $this->creditService->deductSmsCredits($user, $smsParts);
                SmsLog::create([
                    'user_id' => $user->id,
                    'recipient' => $recipient,
                    'message' => $message,
                    'source' => 'Manual',
                    'sms_parts' => $smsParts,
                    'credits_consumed' => $smsParts,
                ]);
            }
            // You might want to add an else block here to notify about failures for specific numbers
        }

        return redirect()->route('sms.send.page')->with('status', 'SMS messages have been queued successfully!');
    }

    /**
     * Sends SMS via the external gateway.
     */
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
            // Log the error for debugging if needed: \Log::error($e->getMessage());
            return false;
        }
    }
}