<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SmsServiceController; // <-- সঠিক কন্ট্রোলার ইম্পোর্ট করা হয়েছে
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('welcome'); });

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/websites', [WebsiteController::class, 'index'])->name('websites.index');
    Route::post('/websites', [WebsiteController::class, 'store'])->name('websites.store');
    Route::delete('/websites/{website}', [WebsiteController::class, 'destroy'])->name('websites.destroy');

    // *** SMS সার্ভিস রাউটগুলো এখন সঠিক কন্ট্রোলার ব্যবহার করছে ***
    Route::get('/sms-service/send', [SmsServiceController::class, 'sendSmsPage'])->name('sms.send.page');
    Route::post('/sms-service/send', [SmsServiceController::class, 'handleSendSms'])->name('sms.send.handle');
    Route::get('/sms-service/history', [SmsServiceController::class, 'smsHistoryPage'])->name('sms.history.page');
});

require __DIR__.'/auth.php';