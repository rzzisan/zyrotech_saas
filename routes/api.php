<?php

use App\Http\Controllers\Api\V1\CourierCheckController;
use App\Http\Controllers\Api\V1\SmsController;
use App\Http\Middleware\VerifyWebsiteDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Courier Check Routes
Route::get('/v1/courier/check', [CourierCheckController::class, 'check'])->middleware(['auth:sanctum', VerifyWebsiteDomain::class]);
Route::post('/v1/courier/batch-check', [CourierCheckController::class, 'batchCheck'])->middleware(['auth:sanctum', VerifyWebsiteDomain::class]);

// SMS Routes
Route::post('/v1/sms/order-status', [SmsController::class, 'sendOrderStatusSms'])->middleware(['auth:sanctum', VerifyWebsiteDomain::class]);

// *** নতুন ক্রেডিট ব্যালেন্স দেখার রাউট ***
Route::get('/v1/sms/credits', [SmsController::class, 'getCredits'])->middleware(['auth:sanctum', VerifyWebsiteDomain::class]);