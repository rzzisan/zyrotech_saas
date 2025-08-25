<?php

use App\Http\Controllers\Api\V1\CourierCheckController;
use App\Http\Middleware\VerifyWebsiteDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// পুরনো সিঙ্গেল চেক রাউট
Route::get('/v1/courier/check', [CourierCheckController::class, 'check'])
        ->middleware(['auth:sanctum', VerifyWebsiteDomain::class])
        ->name('api.v1.courier.check');

// *** নতুন ব্যাচ প্রসেসিং রাউটটি এখানে যোগ করা হয়েছে ***
Route::post('/v1/courier/batch-check', [CourierCheckController::class, 'batchCheck'])
        ->middleware(['auth:sanctum', VerifyWebsiteDomain::class])
        ->name('api.v1.courier.batchCheck');