<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebsiteController; // <-- এই লাইনটি জরুরি
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/websites', [WebsiteController::class, 'store'])->name('websites.store');
Route::delete('/websites/{website}', [WebsiteController::class, 'destroy'])->name('websites.destroy');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // "My Websites" পেজের জন্য নতুন রাউটগুলো এখানে যোগ করা হয়েছে
    Route::get('/websites', [WebsiteController::class, 'index'])->name('websites.index');
    Route::post('/websites', [WebsiteController::class, 'store'])->name('websites.store');
});

require __DIR__.'/auth.php';