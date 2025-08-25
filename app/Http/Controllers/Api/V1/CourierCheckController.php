<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Throwable;

class CourierCheckController extends Controller
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    public function batchCheck(Request $request)
    {
        $validated = $request->validate([
            'phones' => 'required|array',
            'phones.*' => 'string|max:20',
        ]);

        $user = Auth::user();
        $results = [];
        $uniquePhones = array_unique($validated['phones']);

        foreach ($uniquePhones as $phone) {
            $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
            if (empty($cleanedPhone)) {
                $results[$phone] = ['error' => 'Invalid phone number provided.'];
                continue;
            }

            // *** মূল পরিবর্তন: CreditService-এ ফোন নম্বর পাঠানো হচ্ছে ***
            if ($this->creditService->canUseCourierCheck($user, $cleanedPhone)) {
                $results[$phone] = $this->fetchCourierData($cleanedPhone);
                // সফল কলের পর ব্যবহার রেকর্ড করা হচ্ছে
                if (!isset($results[$phone]['error'])) {
                    $this->creditService->recordCourierCheckUsage($user, $cleanedPhone);
                }
            } else {
                $results[$phone] = ['error' => 'Daily or monthly limit exceeded for new numbers.'];
            }
        }

        return response()->json($results);
    }
    
    private function fetchCourierData(string $phoneNumber)
    {
        $apiUrl = "https://portal.packzy.com/api/v1/fraud_check/" . $phoneNumber;
        try {
            $response = Http::withoutVerifying()->timeout(15)->get($apiUrl);
            if ($response->failed()) { return ['error' => 'Courier service returned an error.']; }
            return $response->json();
        } catch (Throwable $e) {
            return ['error' => 'Failed to connect to the external courier API.'];
        }
    }
}