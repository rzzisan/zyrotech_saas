<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use App\Services\CreditService;
use App\Traits\PhoneNumberFormatter; // <-- নতুন Trait ইম্পোর্ট করা হয়েছে
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Throwable;

class SmsController extends Controller
{
    use PhoneNumberFormatter; // <-- নতুন Trait ব্যবহার করা হচ্ছে

    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    public function sendOrderStatusSms(Request $request)
    {
        // ... validation and credit check logic remains the same ...

        // *** মূল পরিবর্তন: Trait থেকে formatPhoneNumberBd() ব্যবহার করা হচ্ছে ***
        $smsSentSuccessfully = $this->sendSmsViaGateway($validated['phone'], $message);

        // ... rest of the logic remains the same
    }

    // ... getCredits() method remains the same ...
    public function getCredits() { /* ... */ }

    private function sendSmsViaGateway(string $recipient, string $message): bool
    {
        try {
            // *** মূল পরিবর্তন: এখন Trait থেকে formatPhoneNumberBd() ব্যবহার করা হচ্ছে ***
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
    
    // private function formatPhoneNumberBd($number) { ... } // <-- পুরনো ফাংশনটি মুছে ফেলা হয়েছে
}